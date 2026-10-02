<?php

declare(strict_types=1);

namespace App\Support\Quality;

use App\Support\Quality\Criteria\AnySpectrumCriterion;
use App\Support\Quality\Criteria\AssignmentsCriterion;
use App\Support\Quality\Criteria\ExperimentFamilyCriterion;
use App\Support\Quality\Criteria\QualityCriterion;
use InvalidArgumentException;

/**
 * Versioned, config-driven evaluator for molecule data-completeness tiers.
 */
final class QualityRubric
{
    /** @var array<string, QualityCriterion> */
    private array $criteria;

    /** @var array<int, array{label: string, requires: list<string|list<string>>}> */
    private array $tiers;

    /** @var list<string> */
    private array $bonuses;

    private int $version;

    /**
     * @param  array<string, mixed>|null  $config
     */
    public function __construct(?array $config = null)
    {
        $config ??= config('quality');
        if (! is_array($config)) {
            throw new InvalidArgumentException('Quality rubric config is missing.');
        }

        $this->version = (int) ($config['version'] ?? 0);
        if ($this->version < 1) {
            throw new InvalidArgumentException('Quality rubric version must be a positive integer.');
        }

        $families = is_array($config['families'] ?? null) ? $config['families'] : [];
        $criteriaConfig = is_array($config['criteria'] ?? null) ? $config['criteria'] : [];
        $this->criteria = $this->buildCriteria($criteriaConfig, $families);

        $this->tiers = $this->normalizeTiers(is_array($config['tiers'] ?? null) ? $config['tiers'] : []);
        $this->bonuses = array_values(array_map('strval', $config['bonuses'] ?? []));

        $this->assertValid();
    }

    public function version(): int
    {
        return $this->version;
    }

    public function evaluate(MoleculeEvidence $evidence): QualityResult
    {
        $met = [];
        foreach ($this->criteria as $key => $criterion) {
            $met[$key] = $criterion->isMet($evidence);
        }

        $tier = 0;
        $tierLabel = 'Not yet rated';
        foreach ($this->tiers as $level => $definition) {
            if ($this->requirementsMet($definition['requires'], $met)) {
                $tier = $level;
                $tierLabel = $definition['label'];
            } else {
                break;
            }
        }

        $criteriaSnapshot = [];
        foreach ($this->criteria as $key => $criterion) {
            if (in_array($key, $this->bonuses, true) || $key === 'any_spectrum') {
                continue;
            }
            $criteriaSnapshot[$key] = $met[$key];
        }

        $bonuses = [];
        foreach ($this->bonuses as $bonusKey) {
            $bonuses[$bonusKey] = $met[$bonusKey] ?? false;
        }

        $nextMissing = [];
        $nextTier = $tier + 1;
        if (isset($this->tiers[$nextTier])) {
            $nextMissing = $this->missingRequirementKeys($this->tiers[$nextTier]['requires'], $met);
        }

        return new QualityResult(
            version: $this->version,
            tier: $tier,
            tierLabel: $tierLabel,
            criteria: $criteriaSnapshot,
            bonuses: $bonuses,
            nextTierMissing: $nextMissing,
        );
    }

    /**
     * Payload shared with the frontend (tiers, labels, docs URL).
     *
     * @return array{
     *     version: int,
     *     docs_url: string,
     *     criteria: array<string, string>,
     *     bonuses: list<string>,
     *     tiers: array<int, array{label: string, requires: list<string|list<string>>}>,
     *     contributor: array{qualifying_tier: int, thresholds: array<int, int>}
     * }
     */
    public function forFrontend(): array
    {
        $criteriaLabels = [];
        foreach ($this->criteria as $key => $criterion) {
            $criteriaLabels[$key] = $criterion->label();
        }

        $contributor = config('quality.contributor', []);

        return [
            'version' => $this->version,
            'docs_url' => (string) config('quality.docs_url', ''),
            'criteria' => $criteriaLabels,
            'bonuses' => $this->bonuses,
            'tiers' => $this->tiers,
            'contributor' => [
                'qualifying_tier' => (int) ($contributor['qualifying_tier'] ?? 4),
                'thresholds' => array_map('intval', $contributor['thresholds'] ?? []),
            ],
        ];
    }

    /**
     * @param  list<string|list<string>>  $requires
     * @param  array<string, bool>  $met
     */
    private function requirementsMet(array $requires, array $met): bool
    {
        foreach ($requires as $requirement) {
            if (is_array($requirement)) {
                $any = false;
                foreach ($requirement as $alt) {
                    if ($met[(string) $alt] ?? false) {
                        $any = true;
                        break;
                    }
                }
                if (! $any) {
                    return false;
                }

                continue;
            }

            if (! ($met[(string) $requirement] ?? false)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  list<string|list<string>>  $requires
     * @param  array<string, bool>  $met
     * @return list<string>
     */
    private function missingRequirementKeys(array $requires, array $met): array
    {
        $missing = [];
        foreach ($requires as $requirement) {
            if (is_array($requirement)) {
                $anyMet = false;
                foreach ($requirement as $alt) {
                    if ($met[(string) $alt] ?? false) {
                        $anyMet = true;
                        break;
                    }
                }
                if (! $anyMet) {
                    $missing[] = implode('|', array_map('strval', $requirement));
                }

                continue;
            }

            if (! ($met[(string) $requirement] ?? false)) {
                $missing[] = (string) $requirement;
            }
        }

        return $missing;
    }

    /**
     * @param  array<string, mixed>  $criteriaConfig
     * @param  array<string, mixed>  $families
     * @return array<string, QualityCriterion>
     */
    private function buildCriteria(array $criteriaConfig, array $families): array
    {
        $criteria = [];
        foreach ($criteriaConfig as $key => $definition) {
            if (! is_array($definition)) {
                throw new InvalidArgumentException("Criterion '{$key}' must be an array.");
            }

            $class = $definition['class'] ?? null;
            if (! is_string($class) || ! class_exists($class)) {
                throw new InvalidArgumentException("Criterion '{$key}' has an invalid class.");
            }

            if ($class === ExperimentFamilyCriterion::class) {
                $familyKey = (string) ($definition['family'] ?? $key);
                if (! isset($families[$familyKey]) || ! is_array($families[$familyKey])) {
                    throw new InvalidArgumentException("Criterion '{$key}' references unknown family '{$familyKey}'.");
                }
                $criteria[$key] = ExperimentFamilyCriterion::fromFamilyConfig($key, $families[$familyKey]);

                continue;
            }

            if ($class === AnySpectrumCriterion::class) {
                $criteria[$key] = new AnySpectrumCriterion(
                    $key,
                    (string) ($definition['label'] ?? 'Any spectrum'),
                );

                continue;
            }

            if ($class === AssignmentsCriterion::class) {
                $criteria[$key] = new AssignmentsCriterion(
                    $key,
                    (string) ($definition['label'] ?? 'Assigned'),
                );

                continue;
            }

            if (! is_subclass_of($class, QualityCriterion::class)) {
                throw new InvalidArgumentException("Criterion class '{$class}' must implement QualityCriterion.");
            }

            /** @var QualityCriterion $instance */
            $instance = app()->make($class, [
                'key' => $key,
                'label' => (string) ($definition['label'] ?? $key),
                'definition' => $definition,
            ]);
            $criteria[$key] = $instance;
        }

        return $criteria;
    }

    /**
     * @param  array<int|string, mixed>  $tiers
     * @return array<int, array{label: string, requires: list<string|list<string>>}>
     */
    private function normalizeTiers(array $tiers): array
    {
        $normalized = [];
        foreach ($tiers as $level => $definition) {
            if (! is_array($definition)) {
                throw new InvalidArgumentException("Tier {$level} must be an array.");
            }
            $requires = $definition['requires'] ?? [];
            if (! is_array($requires)) {
                throw new InvalidArgumentException("Tier {$level} requires must be an array.");
            }
            $normalized[(int) $level] = [
                'label' => (string) ($definition['label'] ?? "Tier {$level}"),
                'requires' => array_values($requires),
            ];
        }
        ksort($normalized);

        return $normalized;
    }

    private function assertValid(): void
    {
        foreach ($this->bonuses as $bonus) {
            if (! isset($this->criteria[$bonus])) {
                throw new InvalidArgumentException("Bonus '{$bonus}' is not a known criterion.");
            }
        }

        $previousKeys = [];
        foreach ($this->tiers as $level => $definition) {
            $keys = $this->flattenRequirementKeys($definition['requires']);
            foreach ($keys as $key) {
                if (! isset($this->criteria[$key])) {
                    throw new InvalidArgumentException("Tier {$level} references unknown criterion '{$key}'.");
                }
            }

            foreach ($previousKeys as $required) {
                if (! in_array($required, $keys, true) && ! $this->coveredByAnyOf($required, $definition['requires'])) {
                    // Soft check: higher tiers should generally supersede lower ones.
                    // Allow "any_spectrum" to drop once specific families appear.
                    if ($required === 'any_spectrum') {
                        continue;
                    }
                }
            }
            $previousKeys = $keys;
        }

        $this->assertNoDuplicateExperimentTokens();
    }

    /**
     * @param  list<string|list<string>>  $requires
     * @return list<string>
     */
    private function flattenRequirementKeys(array $requires): array
    {
        $keys = [];
        foreach ($requires as $requirement) {
            if (is_array($requirement)) {
                foreach ($requirement as $alt) {
                    $keys[] = (string) $alt;
                }

                continue;
            }
            $keys[] = (string) $requirement;
        }

        return array_values(array_unique($keys));
    }

    /**
     * @param  list<string|list<string>>  $requires
     */
    private function coveredByAnyOf(string $key, array $requires): bool
    {
        foreach ($requires as $requirement) {
            if (is_array($requirement) && in_array($key, array_map('strval', $requirement), true)) {
                return true;
            }
            if ($requirement === $key) {
                return true;
            }
        }

        return false;
    }

    private function assertNoDuplicateExperimentTokens(): void
    {
        $seen = [];
        foreach ($this->criteria as $criterion) {
            if (! $criterion instanceof ExperimentFamilyCriterion) {
                continue;
            }

            $family = config('quality.families.'.$criterion->key(), []);
            if (! is_array($family)) {
                continue;
            }

            $nuclei = array_map('strval', $family['nuclei'] ?? []);
            $dimension = $family['dimension'] ?? null;
            foreach ($family['experiments'] ?? [] as $experiment) {
                $token = strtolower((string) $experiment);
                $signature = $token.'|'.($dimension ?? '').'|'.implode(',', $nuclei);
                if (isset($seen[$signature]) && $seen[$signature] !== $criterion->key()) {
                    throw new InvalidArgumentException(
                        "Experiment token '{$token}' with the same nuclei/dimension is claimed by both '{$seen[$signature]}' and '{$criterion->key()}'."
                    );
                }
                $seen[$signature] = $criterion->key();
            }
        }
    }
}
