# Changelog

## [1.1.0-rc.5](https://github.com/NFDI4Chem/nmrxiv/compare/v1.0.0-rc.5...v1.1.0-rc.5) (2022-11-08)


### Features

* enable views to introduce the user to nmrXiv concepts - ref [#553](https://github.com/NFDI4Chem/nmrxiv/issues/553) ([bec2580](https://github.com/NFDI4Chem/nmrxiv/commit/bec2580e17a0bc0de30eda610f2296180cbc6275))
* fixed failing tests ([319964f](https://github.com/NFDI4Chem/nmrxiv/commit/319964f3357f4f79bb27ef94172b54ef7223fdd0))
* labelling datasets/measurements with the nucleus details - ref [#570](https://github.com/NFDI4Chem/nmrxiv/issues/570) ([3d4abe2](https://github.com/NFDI4Chem/nmrxiv/commit/3d4abe245bd65eaf0b759177f541ffcc547c9782))


### Bug Fixes

* added datsets sanitize command ([d3154de](https://github.com/NFDI4Chem/nmrxiv/commit/d3154de883652f6a707359c865ef46315e58b483))
* download controller null draft error handling ([6327143](https://github.com/NFDI4Chem/nmrxiv/commit/63271435b605fd43059655ccb47542092f48e12e))
* draft processing owner id bug fix ([eb23e58](https://github.com/NFDI4Chem/nmrxiv/commit/eb23e58580626eb53ba6086d16b73ad42c122419))
* enabled nmrium data versioning defaults to 3 ([ee2b6a2](https://github.com/NFDI4Chem/nmrxiv/commit/ee2b6a207695b486c2aed99e8910fdf867490234))
* fetching datacite url from env and updates to the assign identifiers query ([ccdb509](https://github.com/NFDI4Chem/nmrxiv/commit/ccdb5094666c1e1f69fc94d139b130ac148f1345))
* generateDOI check is performed for every ds instance ([c5a0e77](https://github.com/NFDI4Chem/nmrxiv/commit/c5a0e77c9adaf39cacc10d7ba6375ea02426cfdc))
* increase post_max_body_size limit. ([e2b2a2b](https://github.com/NFDI4Chem/nmrxiv/commit/e2b2a2bf994ecd3ddd64d85f079154b43a5ac352))
* manage roles added addition check ([fa76719](https://github.com/NFDI4Chem/nmrxiv/commit/fa76719428d597614d3dcd2c85f0fb130d17604c))
* null fields sent to datacite ([8cf3702](https://github.com/NFDI4Chem/nmrxiv/commit/8cf370264f769058f121db440d25378e35dadf83)), closes [#538](https://github.com/NFDI4Chem/nmrxiv/issues/538)
* project and study formatting in cards ref [#568](https://github.com/NFDI4Chem/nmrxiv/issues/568) ([68e10ee](https://github.com/NFDI4Chem/nmrxiv/commit/68e10ee08e0a7c7c325871d694e91e34a30cbbae))
* published sail assets ([bb6a50d](https://github.com/NFDI4Chem/nmrxiv/commit/bb6a50d6a6085cbc3d64ec691bc9ab77f033c685))
* Refactored draft controller owner, team and UI public views updates ([484cdf9](https://github.com/NFDI4Chem/nmrxiv/commit/484cdf9cd3493055b85b760ea079737fad3ab224))
* syncing draft from the project updates ([7e6f6ef](https://github.com/NFDI4Chem/nmrxiv/commit/7e6f6ef1415afaf62d42905f8acea971a719b644))
* updated headline from Meta data -&gt; Metadata ref [#562](https://github.com/NFDI4Chem/nmrxiv/issues/562) ([8fe93df](https://github.com/NFDI4Chem/nmrxiv/commit/8fe93df404fa666ab17e088fc6bf79ebdb90ed25))
* validations view - missing file - 500 error handling - ref [#571](https://github.com/NFDI4Chem/nmrxiv/issues/571) ([804c3a9](https://github.com/NFDI4Chem/nmrxiv/commit/804c3a93086e1eff4666a3e447531555a15be939))

## [1.0.1-rc.4](https://github.com/NFDI4Chem/nmrxiv/compare/v1.0.0-rc.4...v1.0.1-rc.4) (2022-10-25)


### Bug Fixes

* explode bug fix and updated nmrxiv:clean command ([a85044c](https://github.com/NFDI4Chem/nmrxiv/commit/a85044cdf6151c6256fe1282e79436cafdbeac9b))
* removing duplicate dataset types ([da94547](https://github.com/NFDI4Chem/nmrxiv/commit/da9454708dcd1ff37a11074f6ba0f8fcbd5f4c8f))
* Restricting NMRium versions to a maximum of 10 due to size constraints ([39acee6](https://github.com/NFDI4Chem/nmrxiv/commit/39acee62d644bb44c56f8f77c19096cd63be8ad7))

## [1.9.1-beta](https://github.com/NFDI4Chem/nmrxiv/compare/v1.9.0-beta...v1.9.1-beta) (2022-10-24)


### Bug Fixes

* extend DataCite ([fe68b17](https://github.com/NFDI4Chem/nmrxiv/commit/fe68b1767081217877b0d53d8a779494f949a69d)), closes [#526](https://github.com/NFDI4Chem/nmrxiv/issues/526)
* Extended clean command to maintain projects ([cc33a15](https://github.com/NFDI4Chem/nmrxiv/commit/cc33a1554e96b1aee61010fafab59c89ba5b0584))
* Extended unpublish project command, version tag change and code formatting ([d1e9054](https://github.com/NFDI4Chem/nmrxiv/commit/d1e9054e903466920ba4571b7659d05c4862356e))
* Extending drafts to include shared project drafts ([cade25c](https://github.com/NFDI4Chem/nmrxiv/commit/cade25c9bd296faf04db26af844b4b5177794610))
* single file upload - null full path issue fix ([177e954](https://github.com/NFDI4Chem/nmrxiv/commit/177e9546ca36c2b14f9538541e7ff02ffafc74c8))
* updated query to retrieve study files and front end checks added ([710a721](https://github.com/NFDI4Chem/nmrxiv/commit/710a721c5a79de0ab9c1276a8ba222ee274e463e))

## [1.9.0-beta](https://github.com/NFDI4Chem/nmrxiv/compare/v1.8.4-beta...v1.9.0-beta) (2022-10-20)


### Features

* Add dev deployment workflows. ([ab69ff3](https://github.com/NFDI4Chem/nmrxiv/commit/ab69ff3098f19642fba6c249f7897954adbfd0f5))
* Add matomo. ([8b865d5](https://github.com/NFDI4Chem/nmrxiv/commit/8b865d53a239c175d6de0f60a368f4a5576d990b))
* Added new command to clean projects ([37eb5a8](https://github.com/NFDI4Chem/nmrxiv/commit/37eb5a87c930d7c07310b8750e4f3686c4e186a7))
* Heroicons version upgrade ([586d1da](https://github.com/NFDI4Chem/nmrxiv/commit/586d1dad256d44d9f57ced51e9bf314769cd549f))
* NMRium json versioning implemented ([90f98be](https://github.com/NFDI4Chem/nmrxiv/commit/90f98be14f592ce1bd26683fa3e8b7bd7da43838))
* Refactoring, Validation, DOI and Identifier implementation ([60b7e68](https://github.com/NFDI4Chem/nmrxiv/commit/60b7e688c9cf754d8edeecd923e4b2ecaa9ac763))
* Stable identifiers / DOI implementation ([1199cf3](https://github.com/NFDI4Chem/nmrxiv/commit/1199cf395eec42b51960d598342afcff231ab378))
* Update Delete team validations. ([10db574](https://github.com/NFDI4Chem/nmrxiv/commit/10db574878b361607c43f9e5da30e556b3c3175a))


### Bug Fixes

* Add Citation & Author issue. ([3a4ce65](https://github.com/NFDI4Chem/nmrxiv/commit/3a4ce653fadbd580fd6225712fcb4d32c6c5a4ee))
* Add help link to documentation ([85fffc9](https://github.com/NFDI4Chem/nmrxiv/commit/85fffc9e004faf1e4b3cf241f50a3a62527902fa))
* Add star to studies. ([24de317](https://github.com/NFDI4Chem/nmrxiv/commit/24de31703d24c65397feb57bb452bdc2d7fc42d1))
* Add team owner avatar ([41ac50f](https://github.com/NFDI4Chem/nmrxiv/commit/41ac50ffa0f1c0f181a769296e9591ee21b19981))
* Added console commands for data schema migration and other formatting changes ([69d535d](https://github.com/NFDI4Chem/nmrxiv/commit/69d535d34200c54331eac19b86bb4dccf6ca4b12))
* Added schema version to the projects / studies / datasets ([7611a32](https://github.com/NFDI4Chem/nmrxiv/commit/7611a321e04eb8903bfc769de52c0e01920b9ac8))
* Added/Updated commands publish, unpublish, assign dois, config based doi assignments, validation - schema version updates and code formatting ([6bb9925](https://github.com/NFDI4Chem/nmrxiv/commit/6bb9925e53bc2e80e7d91537395af83a3dfd8466))
* Broken link for Author Orcid Id. ([6fc6cb8](https://github.com/NFDI4Chem/nmrxiv/commit/6fc6cb8a6399f03874d8757c4e134e032b8534d9))
* Broken Orcid link. ([1033c04](https://github.com/NFDI4Chem/nmrxiv/commit/1033c043e81a027718552d98522ea09d6bc7198b))
* Broken parent link bug fix ([eb8491d](https://github.com/NFDI4Chem/nmrxiv/commit/eb8491db1d49ebf90e19838dbd23677ed6c6ecf1))
* Broken stable identifier based links bug fix ([131ad47](https://github.com/NFDI4Chem/nmrxiv/commit/131ad47bb0d1bbf5ab8283418016065e67c2c42a))
* Carbon reference import ([c59f69b](https://github.com/NFDI4Chem/nmrxiv/commit/c59f69bf313cc7695243948729fd048a8bbc5d74))
* change the dev workflow name. ([13511cc](https://github.com/NFDI4Chem/nmrxiv/commit/13511cc588fcd3e5e5969aa49aac3e9cd436bd97))
* Code formatting and missing imports fix ([067eff2](https://github.com/NFDI4Chem/nmrxiv/commit/067eff2cf705560f3d2290a56e0f7e6e2e0bcf6b))
* Dashboard reload after project save ([64ad79f](https://github.com/NFDI4Chem/nmrxiv/commit/64ad79fbe4992e2e741ea5661fdf2d6df0ba74d4)), closes [#460](https://github.com/NFDI4Chem/nmrxiv/issues/460)
* Demo banner typo fix and z-index update ([2728561](https://github.com/NFDI4Chem/nmrxiv/commit/27285614133a34726be109c050f26f12dc5085dc))
* Demo site warning message added to local and development environments ([f7b2979](https://github.com/NFDI4Chem/nmrxiv/commit/f7b29797f07e8bbff5454fc6900e4a3f0ca2e31f))
* Disabled workspace specification for the nmrium wrapper ([ea35ac3](https://github.com/NFDI4Chem/nmrxiv/commit/ea35ac3ccf7689ea56050e886641b0bedc9c3798))
* doiService reference bug fix ([b8c936d](https://github.com/NFDI4Chem/nmrxiv/commit/b8c936d34bb7da90e83a2c787918667ea38202b2))
* Extended nmrxiv clean migration console command ([b2eb2c8](https://github.com/NFDI4Chem/nmrxiv/commit/b2eb2c8f44e3972886949b40b6e691ca12bc3442))
* File upload duplication issue fix - studies page ([bd09c51](https://github.com/NFDI4Chem/nmrxiv/commit/bd09c51c51e089b19cae32dd66305e2cd93a740d))
* Fixed broken sharing docs link and code formatting ([2845348](https://github.com/NFDI4Chem/nmrxiv/commit/2845348abb219e70b53fca5483858068f34ef741))
* Fixes - public studies - possible deletion of structures [#464](https://github.com/NFDI4Chem/nmrxiv/issues/464) ([87eaa26](https://github.com/NFDI4Chem/nmrxiv/commit/87eaa26fccd4d2c8ca097f2e7c3455e95676a57b))
* Fixes show roles for project members [#309](https://github.com/NFDI4Chem/nmrxiv/issues/309) ([5cd134d](https://github.com/NFDI4Chem/nmrxiv/commit/5cd134d993df5e66423b6af6a77a41f235e88410))
* Fixes submission pipeline - enable editing structures [#480](https://github.com/NFDI4Chem/nmrxiv/issues/480) ([5bb2eae](https://github.com/NFDI4Chem/nmrxiv/commit/5bb2eae7331f6664c79ae60a5b46c886f114707a))
* Force reloading nmrium ([832a318](https://github.com/NFDI4Chem/nmrxiv/commit/832a318e77352a74e20c05cbc304d32b0c53be58))
* forcing browser to reload previews ([da2c7ec](https://github.com/NFDI4Chem/nmrxiv/commit/da2c7ec99a089995f1d6cfe527251b398ed996aa))
* Increasing upload body size limit ([52bcb31](https://github.com/NFDI4Chem/nmrxiv/commit/52bcb31b7a50d1c19f7e440838558d0d893e88d7))
* Limiting the max projects shown in the welcome page - extended the projects component ([94928f4](https://github.com/NFDI4Chem/nmrxiv/commit/94928f446aa671ba342a426f6586c1fae60cedec))
* Manage studies and datasets button text changes and code formatting (vite) ([af05b02](https://github.com/NFDI4Chem/nmrxiv/commit/af05b026d6b953024dc448252a63138fd9f1ba23))
* migrated public pages to be loaded via public url, code formatting and indentation fixes ([5290afe](https://github.com/NFDI4Chem/nmrxiv/commit/5290afea8d59f99d482eb7f7197613a696477354))
* migrating public share url through identifiers ([e715aae](https://github.com/NFDI4Chem/nmrxiv/commit/e715aaea12149757a1479f8d311c5ccc65c64c3a))
* Missing files in s3 bug fix, FSObject class name typo fix, Publish view updates ([ea8e4e7](https://github.com/NFDI4Chem/nmrxiv/commit/ea8e4e783c93b5d2725d7b960cb7e9971c5d02ca))
* missing SVGString function added, CSS changes and conditional rendering of pagination in the projects & datasets pages ([a762544](https://github.com/NFDI4Chem/nmrxiv/commit/a762544e4eb6f36ff6a5f0113f746ebb6bd06ebb))
* Missing validation class import fixed ([507934f](https://github.com/NFDI4Chem/nmrxiv/commit/507934f85ab92390bf2e87a744679c1ec273ea9c))
* moved getSVGString to the methods section ([1689a81](https://github.com/NFDI4Chem/nmrxiv/commit/1689a8156d95ef0cb37dc7782e74b60dd8d15a94))
* Non existent drafts update bug fix and validation null value bug fix ([732ec31](https://github.com/NFDI4Chem/nmrxiv/commit/732ec3142776009021a2c3f2af346487a746f3b7))
* Passing doiService variable to DB enclosure ([a4b6c1f](https://github.com/NFDI4Chem/nmrxiv/commit/a4b6c1fc85978a30e245d2e3de2b55b221e34341))
* Project delete action is not extended to studies and drafts ([0afd752](https://github.com/NFDI4Chem/nmrxiv/commit/0afd752f723ecbb33d0a17130ba0ff29f512a9f1))
* Public spectral datasets viewer bug fix ([44c1ad3](https://github.com/NFDI4Chem/nmrxiv/commit/44c1ad3fc7c5eaac3c7fe6a770d4ebe49edcbe6e))
* remove console ([9a9bb45](https://github.com/NFDI4Chem/nmrxiv/commit/9a9bb456486658110988ebcf71075680018da5d3))
* remove release-please ([c4df99e](https://github.com/NFDI4Chem/nmrxiv/commit/c4df99e8679a9f9b2656b4b4b283504ca44225fa))
* removed citations requirement for validations ([3085728](https://github.com/NFDI4Chem/nmrxiv/commit/3085728a67dc86098515a924d62692a29ffd4902))
* replace <a> with jetstream link tag ([f31318d](https://github.com/NFDI4Chem/nmrxiv/commit/f31318dae34b56b7aabb1de1d70818a0361ba4da))
* resetting loading indicator incase of NMRium spectra loading error ([8f8125c](https://github.com/NFDI4Chem/nmrxiv/commit/8f8125cb431ec435673e4251d8f0e5f95047b380))
* Retrieving only active drafts, ignoring missing files/folders, updated process function to reflect the same ([bc9497f](https://github.com/NFDI4Chem/nmrxiv/commit/bc9497fb84636f9e55fdcaa23c7400a0438da245))
* Role bug fix ([d419db1](https://github.com/NFDI4Chem/nmrxiv/commit/d419db160acad96a6e558c14d6862b5155128525))
* separate user and team owner situation handling bug fix ([13d4afe](https://github.com/NFDI4Chem/nmrxiv/commit/13d4afe5badb25a273f98b0e752f0ebe385fe9fd))
* spectra undefined bug fix ([7282c4d](https://github.com/NFDI4Chem/nmrxiv/commit/7282c4df2cb303131e08f6e232b3c44fca9390d2))
* Structure edit feature enabled ([566cd62](https://github.com/NFDI4Chem/nmrxiv/commit/566cd62ba3ccab9b0b9b09a119af1c4ba674c493))
* Tracking file status to be active / missing ([984ed2b](https://github.com/NFDI4Chem/nmrxiv/commit/984ed2b1d69600ef57300df6f839b409e98707eb))
* trailing spaces in file names issue fix, empty spectra save disabled and other ui updates ([5577df3](https://github.com/NFDI4Chem/nmrxiv/commit/5577df35545bdfe10e1b3e822c01a760d9eace2a))
* Unify "public " symbol fixes [#399](https://github.com/NFDI4Chem/nmrxiv/issues/399) ([38495ca](https://github.com/NFDI4Chem/nmrxiv/commit/38495ca04bc3a9691fcd59f6fe93f60e159596fa))
* Update Author & Citation header. ([ebdde70](https://github.com/NFDI4Chem/nmrxiv/commit/ebdde70054dc2a1cff37da53dd5f45702f49a0c8))
* Update citation import from DOI. ([3194b16](https://github.com/NFDI4Chem/nmrxiv/commit/3194b16dcd6dcb690e7fa0c3d3c9fb8cd810f3c2))
* Update cond to Need Help link ([e51af33](https://github.com/NFDI4Chem/nmrxiv/commit/e51af33fd7187529fd77a51c8df97d5c3360e959))
* update deployment branch name. ([35afb47](https://github.com/NFDI4Chem/nmrxiv/commit/35afb47206e57581cf6ea07e19465c2436f24118))
* Update manage author API ([a973895](https://github.com/NFDI4Chem/nmrxiv/commit/a973895a6465947bc9f7667f261ff62104e336d3))
* Update project will revalidate project automatcally and file upload disabled in the study files tab (uploaded via submission) ([d316ac4](https://github.com/NFDI4Chem/nmrxiv/commit/d316ac43ded43f0a54c32609f8a3db21790e6f8a))
* Update some links and logo images ([9ab7d80](https://github.com/NFDI4Chem/nmrxiv/commit/9ab7d802302b58bd3f709fbb640d2fe51b66baa4))
* Update team deletion validation msg. ([8ff8f79](https://github.com/NFDI4Chem/nmrxiv/commit/8ff8f79483d6b313063b45f7f59eb5c0f7d00115))
* Update the ingress and letsencrypt. ([c4e6d02](https://github.com/NFDI4Chem/nmrxiv/commit/c4e6d0274a3f089786232a900d6f30d75529d18b))
* Update welcome screenshots ([048e085](https://github.com/NFDI4Chem/nmrxiv/commit/048e0857647f2748154b46894cab98fa2cb5b345))
* Updated daily scheduled command for publishing models ([92c2594](https://github.com/NFDI4Chem/nmrxiv/commit/92c259469ea713bc9fc87023f230375e48da0f88))
* Updated privacy policy and terms pages to reflect Matomo analytics ([4420d31](https://github.com/NFDI4Chem/nmrxiv/commit/4420d311f85024e01167b84acb4dbe865f6165f3))
* Updated product and datasets cards ([a78018e](https://github.com/NFDI4Chem/nmrxiv/commit/a78018ed1ca103978e82a72c77eb18da71c085f9))
* Updated validations module - loaded through config ([c343634](https://github.com/NFDI4Chem/nmrxiv/commit/c343634317d26cb09cf21876296cef73ee4ea4bb))
* Updating stale draft_ids on the file system objects ([dbd38fa](https://github.com/NFDI4Chem/nmrxiv/commit/dbd38fa3b3ff1c813c87047b210f6c0480eaa48c))
* Validation class missing bug fix ([cd994d0](https://github.com/NFDI4Chem/nmrxiv/commit/cd994d048184bd1ada6fd490ad69b6231dfdecf0))

## [1.8.4-beta](https://github.com/NFDI4Chem/nmrxiv/compare/v1.8.3-beta...v1.8.4-beta) (2022-09-09)


### Bug Fixes

* Download path ltrim broken links bug fix ([75380e7](https://github.com/NFDI4Chem/nmrxiv/commit/75380e7751b0a15cbef71a5c2f0b18b70814b951))
* various bug fixes and updates ([d645248](https://github.com/NFDI4Chem/nmrxiv/commit/d645248a2b097ef8484ef8d45c4f937e36fa0d2b))
* Welcome page horizontal overflow and cookie banner hidden element bug resolved ([eea3b9c](https://github.com/NFDI4Chem/nmrxiv/commit/eea3b9c97bc6daea20fa8f4b0b1e1ab6e6985229))

## [1.8.3-beta](https://github.com/NFDI4Chem/nmrxiv/compare/v1.8.2-beta...v1.8.3-beta) (2022-09-08)


### Bug Fixes

* 436 ([67afa1d](https://github.com/NFDI4Chem/nmrxiv/commit/67afa1db4791edc71f82466b89b31c336310fae9))

## [1.8.2-beta](https://github.com/NFDI4Chem/nmrxiv/compare/v1.8.1-beta...v1.8.2-beta) (2022-09-05)


### Bug Fixes

* Public project share url display issue fixed ([6dbaa90](https://github.com/NFDI4Chem/nmrxiv/commit/6dbaa9095756c03cbff5f591717fb6a15dc863c8))
* Public study - dataset info loading issue fixed and js code - formatting ([a9075cf](https://github.com/NFDI4Chem/nmrxiv/commit/a9075cf8f675850356ae5421cddf5e87ee038428))
* Study share url bug fixed ([641b939](https://github.com/NFDI4Chem/nmrxiv/commit/641b9390ad6fb81d32fa7176387ca64d279217f6))

## [1.8.1-beta](https://github.com/NFDI4Chem/nmrxiv/compare/v1.8.0-beta...v1.8.1-beta) (2022-09-02)


### Bug Fixes

* add prelease tag to release-please. ([fe7ec9a](https://github.com/NFDI4Chem/nmrxiv/commit/fe7ec9a517542d8b0e09c885c2615da60ee56216))
* Check release-please. ([0e689ec](https://github.com/NFDI4Chem/nmrxiv/commit/0e689ecc1c7b1f1ba1d88c681a3f703f87557052))
* Updated pre beta tag to beta and nmrium_info lazy loading switch ([43ace74](https://github.com/NFDI4Chem/nmrxiv/commit/43ace74aa2ec9419ea25dc766a05434ad92695d5))
