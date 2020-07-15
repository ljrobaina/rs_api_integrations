# Installation
## Xcode
  Is a suite of software development tools developed by Apple for developing software.
## Homebrew
  Is a software package management system that simplifies the installation of software on Apple's macOS
## Mysql
###### macOS
  Use Homebrew to install MySQL version 5.6
  ```
  brew install mysql@5.6 to your `~/.bash_profile` for easier mysql starting/stopping
  # Add the path 
  'export PATH="/usr/local/opt/mysql@5.6/bin:$PATH"'
  ```
###### Linux (Ubuntu)
  
* RVM (ruby version manager)
* Ruby
  - use RVM to install current ruby version
* GIT
  - may already be installed from Xcode tools
* NVM (node version manager)
* Node
  - use NVM to install current node version
* Redspot
  - use git to clone this repo
  - install bundler
  - use bundler to install gems
  - use npm to install packages
* Redis
  - use homebrew to install redis


# Background Processes
* Sidekiq
  - can be started by running; bundle exec sidekiq
* Delayed Jobs
  - can be started by running; rake jobs:work
* Redis
  - can be started by running; redis-server /usr/local/etc/redis.conf

# Github Hooks 
* post-commit hook
  - run command `npm run add-pc-hook` to create post-commit hook
  - when committing add tag `affects_mobile`, if it affects mobile
  - will generate file `mobile_warning.txt` with details regarding the commit
