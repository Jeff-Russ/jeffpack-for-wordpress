# Parchment WordPress Theme

Parchment is a child theme of Twenty Sixteen emphasizing natural looking paper-like textures. It is still a work in progress.  

<<<<<<< Updated upstream
=======
![deployable: partially](https://s3.amazonaws.com/cdn.shared/git-status-badges/deployable-partially-yellow.svg)  

Jeffpack for WordPress is a undeniably self-serving project but perhaps you'll find it useful. It's the code and the means I use to set up a new WordPress site. The code is (right now) a theme and a plugin. The means is SSH and git on the server. It's designed for someone that prefers looking at their own code editor and a terminal shell over the WordPress Admin Panel. The workflow is much more like working with a Ruby on Rails app on a your local development PC and then pushing it to the server, only it will be a checkout from the server instead. This way you aren't making experimental changes to a live website and you are able to modify the underlying code in your own editor/IDE and not via the WordPress admin panel.  

## This Code

The Jeffpack code is a WordPress Plugin / Theme pair for styling your WordPress sites and individual posts via Shortcodes. The plugin contains much of the functional part of CSS and JavaScript whereas the Theme fills in more visual details capturing the feel of naturally aged paper. Textures and shadows are everywhere but are so subtle they are nearly subliminal.  

Right now the code is set up for one particular site but soon the details of the theme will be extracted out into a customizable set of SASS mixins and functions, allowing  you to make different version that vary certain details of the interface as needed.  

## Your Code

This code is intended to be forked or even just a model for something else. In other words, techniques I'm using with git and organization of code might be of interest to those creating their own WordPress themes and plugins who wish to develop numerous WordPress sites using their own code without re-inventing the wheel each time yet having flexible customizations they decide BEFORE deploying or modifying the live site.  

## How it Works

Git is used to track anything and everything about your site all at once (aside from actual content unless you'd like to). This means the git tracking is at the root of your WordPress site and is set up to not track Core code from WordPress itself. `.gitignore` can be set up to be more like a "git DON'T ignore" in that it whitelists files you'd like to track instead of specify everything you don't:  

    # ignore EVERYTHING first with *
    *

    # now a whitelist of things to NOT ignore using ! operator here:
    !.gitignore
    !README.md

    !sources_and_tests/colorcards-shortcodes-test.css
    !sources_and_tests/colorcards-shortcodes.js
    !sources_and_tests/colorcards-shortcodes.scss
    !sources_and_tests/shortcodes-test.html

    !wp-content/themes/cc-parchmentpaper-wp-theme
    !wp-content/plugins/cc-shortcodes-wp-plugin

(first with `*` then `!whatever`) the code being added and doesn't touch anything in WordPress else unless you do something new in `.gitignore`. Also it's all been specified in `.git/info/exclude` instead of `.gitignore` but you can add more rules by creating a `.gitignore`.  

## Installation 

To get started with installation just `cd` to the root of the WordPress site and checkout various branches in without touching the WordPress Core code like this:  

     $ ssh MY_USERNAME@MYSITE.COM
     password: ********
    ~$ cd ROOT_DIRECTORY_OF_YOUR_WORDPRESS_INSTALLATION
    ~$ git init
    ~$ git remote add origin git@github.com:Jeff-Russ/jeffpack-for-wordpress.git

 Since it set up for my tastes I would recommend forking it and doing that last line like this instead:  

    ~$ git@github.com:YOUR_GH_USERNAME/YOUR_FORK.git

Then: 

    ~$ git fetch
    ~$ git checkout -t origin/master   # or any branch really

Without `-t` or `--track` you will be in 'detached HEAD' state which is probably not what you want. I've tested the safety of the above commands in term of keeping what files you have intact and I can verify that if you have any files with matching paths and names, git will do nothing and you will get a notice like this:  

    error: The following untracked working tree files would be overwritten by checkout:
        README.md
    Please move or remove them before you can switch branches.
    Aborting

At this point you can enable whatever you want from the admin panel.  

## Editing Locally 

On your local computer, do the same as above at the root of a matching WordPress directory you want to use for working out changes. You should definitely keep a branch (probably master) to always match the state on the live site and never modify it directly. You'll be pushing to the repository such as GitHub and not your live server directly although you could set it up to be like a repository and push to it as a means of deploying and updating but that setup is a bit more complicated.  

## Updating the Live Site

Whatever you commit and push to the repository can then be available to the from the live site via git. You can even quickly checkout an experimental branch on the live site and then quickly revert.  

## Backing Up Your Site

You might actually choose to whitelist more of the files that come with WordPress as a means of backing them up but I would recommend not having a public repo at that point. Also, you repo looses the ability to be reused for other sites this way and, if certain files that reference the host domain are git tracked, you can render your site stuck on that host unless you go in and modify the code. The following tricks are not essential to using this code and are merely here as a reminder that such things are possible!  

### Method 1

There is a solution that solve both problems. Whenever you run `git` it assumes you are using the settings in `.git/` of your current working directory but you can force `.git/` to be named something else, even something that is not hidden (does not have start with the dot `.`) both when you create a new git repository and whenever you run any git command afterward. This way you can have to two git trackings of the same content! With the following, you can initialize a second git tracking which uses a new git directory which will be created with the name `.git-for-backup` instead of `.git`. 

    ~$ git --git-dir=.git-for-backup init

Now whatever you run starting with `git --git-dir=.git-for-backup` actually applies to this other git project. You could even make Bash command such as `git-backup` that is an alias for `git --git-dir=.git-for-backup`. Remember that the whitelisting was specified in `.git/info/exclude` so we are clear in this new `.git-backup` to have a fresh set of rules, although if you had a `.gitignore` it will be used by both.  At this point you can commit everything to `.git-backup` and push it somewhere else, then go back to typing `git` normally as if the second git isn't even there!  

### Method 2 ALL WRONG

First some terminology: the normally named `.git/` directory is called the __"Git Directory"__ and is what `--git-dir` is referring to. This could actually be a full path. The directory you are tracking on your development machine and all of it's contents are called the __"Main Working Tree."__ This can be considered the root of your directory. Everything above (outside of) this main working tree is not tracked by git. Typically `.git` is at the root of your working tree but they could actually not be in the same location, You could have `.git` deeper into your Main Working Tree. When doing this, things can get a bit confusing.  

    mkdir maintree
    cd maintree
    touch fileinmain
    mkdir subdir
    git --git-dir=subdir/.git init
    cd subdir
    touch fileinsub
    git add .
    git commit -m "commit from subdir"

At this point if you pushed to a repository and looked at it online you would see the repository is IN `subdir` and you would see `fileinsub` directly in it but no sign of `fileinmain`, since it would be a level up from the entire repository. You might have initialized while in `maindir` but your main directory tree was set at `subdir` when because you ran `add` and `commit` from there. Here is where things get strange. Even after all that if you do this:  

    cd ../ # back up to maindir
    git --git-dir=subdir/.git add .
    git --git-dir=subdir/.git commit -m "commit from maindir"
    git --git-dir=subdir/.git push origin master

Now if you have a look at what happened you'll now see `fileinmain`, `subdir` and `subdir`/`fileinsub`! You just shifted the entire repository up a level and added the parent directory's contents! You could even go up all the way to the root of your drive and commit your whole computer if you had some sane reason! Now if you were to do the opposite and try to reposition to make the `subdir` be the main file tree again it would not have any effect.  

So let's say you have a directory full of different repositories and you want to work from their common parent directory, which is not directly tracked by git. Let's look at an example like that but for simplicity, only one repo in it.  

    mkdir bunchofgits
    cd bunchofgits
    touch donttrackme
    mkdir git1
    touch git1/trackme
    git --git-dir=git1/.git status # you will see donttrackme
    git --work-tree=git1 --git-dir=git1/.git status # you WON'T see it

The second is as if you `cd` into it before running the command, which would also mean you wouldn't need `--git-dir` As of git 1.8.5 you can use `-C` to have git `cd` before whatever you run and then return you back making this the same as the last line above:  

    git -C git1 status

This is much better! 




If you were to fork this repository to be at the `wp-content` as it's root rather than a level up you could have a `.git/` there and another one a level up to track the entire WordPress site. This way you could `cd` up and down a level to do your git commands or do it all from the top level like this:  

    $ git status                        # check status of whole thing
    $ git --work-tree=wp-content status # check status of git in wp-content

The `--work-tree` option is basically the same as `--git-dir` only you don't need `.git` at the end and it's always assume to be named `.git` not something else. But there is actually another way...  

### Method 3

Starting [git 1.8.5](https://github.com/git/git/blob/5fd09df3937f54c5cfda4f1087f5d99433cce527/Documentation/RelNotes/1.8.5.txt#L115-L116) you can use the `git -C subdir status` as you would use `git --work=subdir status`

## Future Plans

Future plans involve expanding to a modular set Themes and Plugins all working with the same set of CSS classes. Each Theme can work with any Plugin and visa versa, where the choice of theme will be a way to change to alternate coloring and layout options.  

Refer the the individual README.md's in component's directory for further information.  
>>>>>>> Stashed changes
