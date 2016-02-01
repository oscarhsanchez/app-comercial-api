set :stage, :production

server 'efr-api', roles: %w{web app db}, port: 22
set :branch, "production_migration_deploy"
set :deploy_to, "/var/www/vhosts/efinanzas-api/httpdocs"

# Make sure we really want to target this environment!
puts "\e[0;35m ==================================================\e[0m\n"
puts "\e[0;35m   Are you sure you want to deploy to PRODUCTION?\e[0m\n"
puts "\e[0;35m ==================================================\e[0m\n"

# Fetch user response
ask :answer, "\e[0;32m [y]es or [n]o?\e[0m"

# Abort if required
unless fetch(:answer) == 'y'
  puts "\e[0;31m Aborting......\e[0m\n"
  exit
end