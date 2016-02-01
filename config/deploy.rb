# deploy.rb
set :stages,        %w(production development local error)
set :default_stage, "error"
set :stage_dir,     "config/deploy"

set :application, 'efinanzas-api'
set :repo_url, 'git@bitbucket.org:esocialtech/efinanzas-api.git'
set :scm, :git
set :format, :pretty
set :log_level, :debug
set :keep_releases, 5

set :ssh_options, { user: 'root' }

set :linked_files, ['application/config/database.php', 'prod.htaccess']
set :linked_dirs, ['application/logs', 'system/cache']

desc "Check that we can access everything"
task :check_write_permissions do
  on roles(:all) do |host|
    if test("[ -w #{fetch(:deploy_to)} ]")
      info "#{fetch(:deploy_to)} is writable on #{host}"
    else
      error "#{fetch(:deploy_to)} is not writable on #{host}"
    end
  end
end

namespace :deploy do

    desc 'Restart application'
    task :restart do
        on roles(:app), in: :sequence, wait: 5 do
            # Your restart mechanism here, for example:
            # execute :touch, release_path.join('tmp/restart.txt')
        end
    end
    after :restart, :clear_cache do
        on roles(:web), in: :groups, limit: 3, wait: 10 do
            # Here we can do anything such as:
            # within release_path do
            # execute :rake, 'cache:clear'
            # end
        end
    end

    after :finishing, 'deploy:cleanup'
end
