SELECT setval('announcements_id_seq', max(id)) FROM announcements;
SELECT setval('assays_id_seq', max(id)) FROM assays;
SELECT setval('audits_id_seq', max(id)) FROM audits;
SELECT setval('author_project_id_seq', max(id)) FROM author_project;
SELECT setval('authors_id_seq', max(id)) FROM authors;
SELECT setval('citation_project_id_seq', max(id)) FROM citation_project;
SELECT setval('citations_id_seq', max(id)) FROM citations;
SELECT setval('dataset_invitations_id_seq', max(id)) FROM dataset_invitations;
SELECT setval('dataset_molecule_id_seq', max(id)) FROM dataset_molecule;
SELECT setval('dataset_user_id_seq', max(id)) FROM dataset_user;
SELECT setval('datasets_id_seq', max(id)) FROM datasets;
SELECT setval('drafts_id_seq', max(id)) FROM drafts;
SELECT setval('failed_jobs_id_seq', max(id)) FROM failed_jobs;
SELECT setval('file_system_objects_id_seq', max(id)) FROM file_system_objects;
SELECT setval('licenses_id_seq', max(id)) FROM licenses;
SELECT setval('linked_social_accounts_id_seq', max(id)) FROM linked_social_accounts;
SELECT setval('markable_reactions_id_seq', max(id)) FROM markable_reactions;
SELECT setval('markable_likes_id_seq', max(id)) FROM markable_likes;
SELECT setval('markable_favorites_id_seq', max(id)) FROM markable_favorites;
SELECT setval('markable_bookmarks_id_seq', max(id)) FROM markable_bookmarks;
SELECT setval('migrations_id_seq', max(id)) FROM migrations;
-- model_has_permissions -- combi
-- model_has_roles SELECT setval('molecule_sample_id_seq', max(id)) FROM molecule_sample; -- combi
SELECT setval('molecule_sample_id_seq', max(id)) FROM molecule_sample;
SELECT setval('molecules_id_seq', max(id)) FROM molecules;
SELECT setval('nmrium_id_seq', max(id)) FROM nmrium;
-- SELECT setval('notifications_id_seq', max(id)) FROM notifications; UUID
-- SELECT setval('password_resets_id_seq', max(id)) FROM password_resets; no pkey
SELECT setval('permissions_id_seq', max(id)) FROM permissions;
SELECT setval('personal_access_tokens_id_seq', max(id)) FROM personal_access_tokens;
SELECT setval('project_invitations_id_seq', max(id)) FROM project_invitations;
SELECT setval('project_user_id_seq', max(id)) FROM project_user;
SELECT setval('projects_id_seq', max(id)) FROM projects;
-- role_has_permissions SELECT setval('role_has_permissions_id_seq', max(id)) FROM role_has_permissions; -- combi
SELECT setval('roles_id_seq', max(id)) FROM roles;
SELECT setval('samples_id_seq', max(id)) FROM samples;
-- SELECT setval('sessions_id_seq', max(id)) FROM sessions; UUID
SELECT setval('studies_id_seq', max(id)) FROM studies;
SELECT setval('study_invitations_id_seq', max(id)) FROM study_invitations;
SELECT setval('study_user_id_seq', max(id)) FROM study_user;
-- SELECT setval('taggables_id_seq', max(id)) FROM taggables; no pkey
SELECT setval('tags_id_seq', max(id)) FROM tags;
SELECT setval('team_invitations_id_seq', max(id)) FROM team_invitations;
SELECT setval('team_user_id_seq', max(id)) FROM team_user;
SELECT setval('teams_id_seq', max(id)) FROM teams;
SELECT setval('templates_id_seq', max(id)) FROM templates;
SELECT setval('tickers_id_seq', max(id)) FROM tickers;
SELECT setval('users_id_seq', max(id)) FROM users;
SELECT setval('validations_id_seq', max(id)) FROM validations;
SELECT setval('versions_version_id_seq', max(version_id)) FROM versions;

-- Make sure you have all the constraints in place
