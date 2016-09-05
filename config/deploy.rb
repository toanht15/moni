set :application, "brandco.jp"
set :repository,  "."
require 'capistrano/ext/multistage'
set :scm, :git
namespace :deploy do
  desc 'set maintenance'
  task :maintenance, :roles => :web do
    clear_crontab
    from = "#{path}/apps/config/app.yml.maintenance"
    to   = "#{path}/apps/config/app.yml"
    run 'cp -f ' + from + ' ' + to
  end

  desc 'rollback to production from maintenance'
  task :unmaintenance do
    from = "#{path}/apps/config/app.yml.product"
    to   = "#{path}/apps/config/app.yml"
    run 'cp -f ' + from + ' ' + to
    set_crontab
  end

  desc 'clear crontab'
  task :clear_crontab, :roles => :batch do
    run "~/.rbenv/shims/whenever -c -f #{path}/config/schedule.rb"
  end

  desc 'set crontab'
  task :set_crontab, :roles => :batch do
    run "~/.rbenv/shims/whenever -i -f #{path}/config/schedule.rb"
  end

end