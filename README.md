# Jeffpack for WordPress

![current cycle: development](https://s3.amazonaws.com/cdn.shared/git-status-badges/current_cycle-development-yellow.svg)  

![deployable: partially](https://s3.amazonaws.com/cdn.shared/git-status-badges/deployable-partially-yellow.svg)  

Jeffpack for WordPress is a undeniably self-serving project but perhaps you'll find it useful. It's the code and the means I use to set up a new WordPress site. Right now the Jeffpack code itself is a WordPress Plugin / Theme pair for styling your WordPress sites and individual posts via Shortcodes. The means is the shell terminal and Git on the server. It's designed for someone that prefers looking at their own code editor and a terminal shell over the WordPress Admin Panel. The workflow is much more like working with a Ruby on Rails app on a your local development PC and then pushing it to the server (only it will be updated from from the server's end instead). This way you aren't making experimental changes to a live website and you are able to modify the underlying code in your own editor/IDE and not via the WordPress Admin Panel.  

## This Code

The plugin contains much of the functional part of CSS and JavaScript whereas the Theme fills in more visual details capturing the feel of naturally aged paper. Textures and shadows are everywhere but are so subtle they are nearly subliminal.  

Right now the code is set up for one particular site but soon the details of the theme will be extracted out into a customizable set of SASS mixins and functions, allowing  you to make different version that vary certain details of the interface as needed.  

## Your Code

This code is intended to be forked or even just a model for something else you make. In other words, techniques I'm using with Git and organization of code might be of interest to those creating their own WordPress themes and plugins who wish to develop numerous WordPress sites using their own code without re-inventing the wheel each time yet having flexible customizations they decide BEFORE deploying or modifying the live site.  

## How it Works

Git is used to track anything and everything about your site all at once (aside from actual content unless you'd like to, although this can cause problems). This means the git tracking is at the root of your WordPress site and is set up to not track the code from WordPress itself. `.gitignore` can be set up to be more like a "Git DON'T ignore" in that it whitelists files you'd like to track instead of specifying everything you don't. It looks like this:  

    # FIRST: Ignore everything first with * wildcard 
    *
    
    # SECOND: Whitelist whatever you want NOT ignored with ! operator
    !.gitignore
    !README.md
    # .... more stuff
    
    # LAST: Ignore things possibly found in whitelisted directories
    # .... more stuff

You can see the full file [here](.gitignore)

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

On your local computer, do the same as above at the root of a matching WordPress directory you want to use for working out changes. You should definitely keep a branch (probably master) to always match the state on the live site and never modify it directly. You might consider having different branches be different actual sites or even multiple Git tracking of the same code (more on that later). You'll be pushing to the repository such as GitHub and not your live server directly although you could set it up to be like a repository and push to it as a means of deploying and updating but that setup is a bit more complicated and not covered here.  

## Updating the Live Site

Whatever you commit and push to the repository can then be available to the from the live site via git. You can even quickly checkout an experimental branch on the live site and then quickly revert.  

## Backing Up Your Site

You might actually choose to whitelist more of the files that come with WordPress as a means of backing them up but I would recommend not having a public repository storing this in that case. Also, you repository looses the ability to be reused for other sites this way and, if certain files that reference the host domain are git tracked, you can render your site stuck on that host unless you go in and modify the code. I have some tricks detailing some possible solutions in [THIS README](jeffpack-non-executing/additional-docs/git-tricks.md).  

## Future Plans

Future plans involve expanding to a modular set Themes and Plugins all working with the same set of CSS classes. Each Theme can work with any Plugin and visa versa, where the choice of theme will be a way to change to alternate coloring and layout options.  

Refer the the individual README.md's in component's directory for further information.  
