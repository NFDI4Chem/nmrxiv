create extension if not exists rdkit;
select * into mols from (select id,mol_from_smiles(canonical_smiles::cstring) m  from molecules) tmp where m is not null;
create index molidx on mols using gist(m);
alter table mols add primary key (id);

-- select * from molecules where identifier = 'CNP0228556' limit 24 offset 0;
-- SELECT * from properties where molecule_id = 139702;
-- select id, COUNT(*) OVER () from molecules where name = 'Curcumin' limit 24;
-- select count(*) from molecules where smiles@>'c1cccc2c1nncc2' ;
-- select id, COUNT(*) OVER () from molecules where LOWER(synonyms) LIKE '%' || LOWER('Thein') || '%' limit 24 offset 0;
-- select id, COUNT(*) OVER () from molecules where LOWER(name) LIKE LOWER('Caffeine') OR LOWER(synonyms) LIKE '%' || LOWER('Caffeine') || '%' ORDER BY CASE WHEN (name) LIKE LOWER('Caffeine') THEN 1 WHEN LOWER(synonyms) LIKE '%' || LOWER('Caffeine') || '%' THEN 2 END;

select id, torsionbv_fp(m) as torsionbv,morganbv_fp(m) as mfp2, featmorganbv_fp(m) as ffp2 into fps from mols;
create index fps_ttbv_idx on fps using gist(torsionbv);
create index fps_mfp2_idx on fps using gist(mfp2);
create index fps_ffp2_idx on fps using gist(ffp2);
alter table fps add primary key (id);

-- select identifier, standard_inchi, standard_inchi_key, properties.score from molecules RIGHT JOIN properties ON properties.molecule_id = molecules.id;