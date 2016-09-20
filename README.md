# Jeffpack for WordPress

![current cycle: development](https://s3.amazonaws.com/cdn.shared/git-status-badges/current_cycle-development-yellow.svg)  

![deployable: partially](https://s3.amazonaws.com/cdn.shared/git-status-badges/deployable-partially-yellow.svg)  

Jeffpack for WordPress is a undeniably self-serving project but perhaps you'll find it useful. It's the code and the means I use to set up a new WordPress site. The code is (right now) a theme and a plugin. The means is SSH and git on the server. The workflow is much more like working with a Ruby on Rails app on a your local development PC and then pushing it to the server, only it will be a checkout from the server instead. This way you aren't making experimental changes to a live website and you are able to modify the underlying code in your own editor/IDE and not via the WordPress admin panel. 

The Jeffpack code is a WordPress Plugin / Theme pair for styling your WordPress sites and individual posts via Shortcodes. The plugin contains much of the functional part of CSS and JavaScript whereas the Theme fills in more visual details capturing the feel of naturally aged paper. Textures and shadows are everywhere but are so subtle they are nearly subliminal.  

## Installation 

The `.gitignore` is more like a "git DON'T ignore" in that it whitelists the code being added and doesn't touch anything in WordPress else unless you change the `.gitignore`. Just `cd` to the root of the WordPress site and checkout various branches in without touching the WordPress Core code like this:  

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

You might actually choose to whitelist more of the files that come with WordPress as a means of backing them up but I would recommend not having a public repo at that point. Also, you repo looses the ability to be reused for other sites this way and, if certain files that reference the host domain are tracked, you can render your site stuck on that host.  

## Future Plans

Future plans involve expanding to a modular set Themes and Plugins all working with the same set of CSS classes. Each Theme can work with any Plugin and visa versa, where the choice of theme will be a way to change to alternate coloring and layout options.  

Refer the the individual README.md's in component's directory for further information.  