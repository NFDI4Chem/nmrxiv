# Why each experiment matters

This page explains the chemistry behind nmrXiv's [data completeness tiers](./overview.md), written for chemists and natural-product scientists.

## ¹H and ¹³C — the baseline

A ¹H spectrum and a ¹³C spectrum (or APT) are the minimum for characterising a structure. Together they establish the proton and carbon inventory of the molecule. Tier 2 ("1D characterised") requires both.

## HSQC and HMBC — building blocks and links

**HSQC** (and HMQC / edited-HSQC) correlates each proton with the carbon it is attached to (one bond). It gives the CH, CH₂ and CH₃ building blocks of the molecule.

**HMBC** correlates protons with carbons two to three bonds away. It links fragments across quaternary carbons and heteroatoms (C=O, O, N). It cannot tell a 2-bond correlation from a 3-bond one, and 4-bond correlations sometimes appear, so alone it leaves constitutional ambiguity.

Tier 3 ("Heteronuclear correlation") requires ¹H, ¹³C, and **either** HSQC or HMBC — enough to start assembling the carbon skeleton.

## COSY / TOCSY — why tier 3 is not enough for tier 4

**COSY** (and TOCSY) shows proton–proton coupling, usually over three bonds (H–C–C–H). It:

- Gives **unambiguous neighbours**. Two protons that correlate in COSY are almost always on adjacent carbons. With only HMBC you are guessing whether a correlation is 2 or 3 bonds, and a wrong guess puts you in the wrong isomer.
- Supports the standard natural-product workflow: HSQC for the CHₙ units → COSY/TOCSY to join them into fragments → HMBC to stitch the fragments together across non-protonated positions.
- Resolves overlapping multiplets in glycosides, steroids and terpenoids by spreading them into a second dimension.
- Gives each C–C bond **independent support**, which referees expect for a new structure.

COSY matters less for molecules with few hydrogens or mostly quaternary carbons (heavily substituted aromatics, some alkaloids), where TOCSY, H2BC, 1,1-ADEQUATE or careful coupling analysis can stand in. The scoring family for COSY also accepts TOCSY tokens for that reason.

Tier 4 ("Full elucidation set") therefore requires ¹H, ¹³C, COSY/TOCSY, HSQC **and** HMBC — the full connectivity set.

## DEPT — bonus, not a substitute for ¹³C

**DEPT** reports carbon multiplicity (CH / CH₂ / CH₃) but does not observe quaternary carbons, so it cannot replace a ¹³C spectrum. A multiplicity-edited HSQC gives the same multiplicity information with better sensitivity once ¹³C and HSQC are present. Many labs skip DEPT entirely.

**Natural-product caveat:** with sub-milligram samples a ¹³C spectrum is often not recorded. Carbon shifts then come from HSQC (protonated carbons) and HMBC (quaternary carbons). That workflow is common in practice; the current rubric still asks for an explicit ¹³C (or APT) for tier 2 so the rule stays transparent.

## NOESY / ROESY — relative configuration (bonus)

**NOESY** and **ROESY** detect through-space contacts between hydrogens less than about 5 Å apart. They give relative configuration (cis/trans, ring fusions, axial/equatorial) and conformation.

They do **not** help establish connectivity (constitution), which is what the star tiers measure. Many compounds are achiral or have no stereocentres, so requiring NOESY would unfairly cap those compounds. ROESY is preferred for mid-sized molecules (~700–1500 Da) where the NOESY signal can vanish — many glycosides and peptides fall in that range.

Neither experiment gives absolute configuration, which needs ECD, X-ray crystallography or Mosher esters. Coupling constants from the 1D or J-resolved spectrum can also support relative configuration without NOESY.

## Assignments — why they earn five stars

Tier 5 requires the full elucidation set **plus** atom-to-peak assignments. Assigned spectra make the data reusable for:

- dereplication and spectral search
- chemical-shift prediction and machine learning
- peer review of proposed structures

You can supply assignments in either of two ways:

1. The **Assignments** tab on a dataset (ACS-style free text and/or an atom–peak table)
2. Linking atoms to signals in **NMRium** (non-empty `diaIDs` on ranges or zones)
