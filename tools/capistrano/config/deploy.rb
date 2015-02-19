set :production, "tb1.veeps.net.au"
set :staging, "tb1.veeps.net.au"
set :testing, "tb1.veeps.net.au"

if ENV["SERVER"] && ENV["SERVER"] == "production"
    set :primary_server, production
    set :user, "wqdev"
    set :path, "thebedroom/production"
elsif ENV["SERVER"] && ENV["SERVER"] == "staging"
    set :primary_server, staging
    set :user, "wqdev"
    set :path, "thebedroom/staging"
elsif ENV["SERVER"] && ENV["SERVER"] == "test"
    set :primary_server, testing
    set :user, "wqdev"
    set :path, "thebedroom/testing"
else
    set :primary_server, "10.2.33.58"
    set :user, "wqdev"
end

set :application, "thebedroom"
set :domain,      "http://www.thebedroom.com.au"
set :deploy_to,   "/var/www/#{path}/htdocs"
set :use_sudo, false
set :repository,  "ssh://jenkins@stash.webqem.com:7999/bed/thebedroom-magento.git"
set :scm,         :git
set :branch, ENV["BRANCH_NAME"]
set :user, "wqdev"
set :permission_script, "/opt/fix-magento-permissions.sh"

role :web, primary_server   # Your HTTP server, Apache/etc
role :app, primary_server   # This may be the same as your `Web` server or a separate administration server

set  :keep_releases,  3

set :app_symlinks, ["/media", "/var", "/sitemaps", "/staging"]
set :app_shared_dirs, ["/app/etc", "/sitemaps", "/media", "/var", "/staging"]
set :app_shared_files, ["/app/etc/local.xml", "/robots.txt"]

task :resetpermissions do
  servers = find_servers_for_task(current_task)
  servers.each do |s|
    permissions_command="ssh #{user}@#{s} \"cd #{deploy_to} && sudo #{permission_script}\""
    puts permissions_command
    exec(permissions_command)
  end
  # whatever you need to do
end

task :flushmemcached do
  servers = find_servers_for_task(current_task)
  servers.each do |s|
    flush_memcached="ssh #{user}@#{s} \"echo 'flush_all' | netcat 127.0.0.1 11211 -q1\""
    puts flush_memcached
    exec(flush_memcached)
  end
  # whatever you need to do
end
