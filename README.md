## Install Redspot
### Xcode
  * Is a suite of software development tools developed by Apple for developing software.
### Homebrew
  * Is a software package management system that simplifies the installation of software on Apple's macOS
### Mysql
###### macOS
  1. Use Homebrew to install MySQL version 5.6 `brew install mysql@5.6`
  2. Add the path to your `~/.bash_profile` for easier mysql starting/stopping `'export PATH="/usr/local/opt/mysql@5.6/bin:$PATH"'`
###### Linux (Ubuntu)
 1. Install using `sudo apt install mysql-server-5.6`
 2. Check the status `sudo service mysql status`
### RVM (Ruby Version Manager)
* Install RVM from https://rvm.io/ 
* Install **Ruby 2.4.1** with RVM `rvm install ruby 2.4.1`
 - Optional
  * Create a gemset `rvm gemset create redspot`
  * Set gemset as default `rvm use ruby-2.4.1@redspot --default`
###### Linux (Ubuntu)
  * Install gnupg2 `sudo apt install gnupg2` if not available
### GIT
 * Install Git
 ###### macOS
 May already be installed from Xcode tools
 `brew install git`
 ###### Linux (Ubuntu)
 `sudo apt install git`
### NVM (Node Version Manager)
* Install from [https://github.com/nvm-sh/nvm#install--update-script](https://github.com/nvm-sh/nvm#install--update-script)
* Install node using NVM `nvm install --lts`
* Install NPM (Node Package Manager) 
###### macOS
`brew install npm`
###### Linux (Ubuntu)
`sudo apt install npm`
### Redis
* Install Redis server from https://redis.io/topics/quickstart
### Redspot
#### Git
* Generate an SSH key if don't have one [https://docs.github.com/en/github/authenticating-to-github/generating-a-new-ssh-key-and-adding-it-to-the-ssh-agent]
* Add ssh key to your github account [https://docs.github.com/en/github/authenticating-to-github/adding-a-new-ssh-key-to-your-github-account]
* Clone repo using SSH. Example: `git clone git@github.com:arthrex/redspot.git`
* Checkout the actual release dev brach Example: `git checkout <branch_name>`
#### Install APP
* Install bundle gem `gem install bundle`
* Navigate to the app directory and do `bundle instal`
- If you get an error installing *mysql2* gem check the messages probably is missing a library
###### macOS
`brew install libmysqlclient-dev`
###### Linux (Ubuntu)
`sudo apt-get install libmysqlclient-dev`
- If you get an error installing *rmagick* gem check if *ImageMagick version (<= 6.4.9)* is already install, if not install it using  
###### Linux (Ubuntu)
```
sudo apt-get install graphicsmagick-libmagick-dev-compat
sudo apt-get install imagemagick`
sudo apt-get install libmagickcore-dev
sudo apt-get install libmagickwand-dev
```
###### macOS
Use home brew to install packages
* Before run the migration:
- Create `database.yml` file from example file
- Create `integrations/arthrex.yml` from example file
- Create DB by enviroment with UTF8 enconde direcly in the DB server.
- Comment lines in `schema.rb` file that start with *add_foreign_key* at the end of the file.
- Comment line 486 in `seeds.rb` which return an error.
###### Linux (Ubuntu)
- Install the English dictionary `sudo apt-get install wamerican`



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
