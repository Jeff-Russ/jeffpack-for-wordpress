# Multi-Git Jeffpack on WordPress

![current cycle: development](https://s3.amazonaws.com/cdn.shared/git-status-badges/current_cycle-development-yellow.svg)  

![deployable: partially](https://s3.amazonaws.com/cdn.shared/git-status-badges/deployable-partially-yellow.svg)  

Multi-Git allows you to track multiple aspects of a a WordPress live site, a WordPress development environment or a fork of Jeffpack for WordPress. In other words you can have a private git repository that backups up every file and folder on a live site, another repository that only tracks the your own code and so forth.  

## About the Current State of this Plugin

As of right now, there are three functioning Bash scripts which act as replacement to the `git` command which will eventually be used programmatically by the plugin itself. These are also executables at the root of the WordPress which can be used as commands (these actually just call the Bash scripts in the plugin) at the root of the WordPress:  

    $ ./git status       # check the status of the jeffpack portions
    $ ./git-backup       # check the status of all essential files
    $ ./git-move         # check the status of all portable portions

The first does not include any code from WordPress itself whereas that latter two do. The difference between those is that the `.git-move` excludes all files that would break a WordPress site if moved to another host.  

## Getting Started

You may need to run the following from the wordpress root: 

    chmod 755 ./git .git-backup .git-move 
    cd wp-content/plugins/jeffpack-git
    chmod 755 git-jeffpack-only.sh git-backup-all.sh git-move-host.sh