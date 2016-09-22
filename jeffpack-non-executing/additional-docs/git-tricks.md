## Multiple Git on the Same Code with --git-dir

Whenever you run `git` it assumes you are using the settings in `.git/` of your current working directory or somewhere any number of levels up from that directory but you can force `.git/` to be named something else, even something that is not hidden (does not have start with the dot `.`). This doesn't just apply to the directory name, it also applies to the full path of it. This can be done both when you create a new git repository and whenever you run any git command afterward. This means you can select different Git trackings of the same content at will! With the following, you can initialize a second git tracking which uses a new git directory which will be created with the name `.git-for-backup/` instead of `.git/`. 

    ~$ git --git-dir=.git-for-backup init

Now whatever you run starting with `git --git-dir=.git-for-backup` actually applies to this other git project. You could even make Bash command such as `git-backup` that is an alias for `git --git-dir=.git-for-backup`.  

By the way, instead of `=` you can just put a space, but you'll more often see people using `=`.  

### Understanding How Git Works with File and Directory Paths

First we need to get some terminology clear: the normally named `.git/` directory is called the __"Git Directory"__ and is what `--git-dir` is referring to. This could actually be a full path. The directory you are tracking on your development machine and all of it's contents are called the __"Main Working Tree."__ This can be considered the root of Git repository. Everything above (outside of) this main working tree is not tracked by Git. Typically `.git/` is at the root of your working tree but they could actually not be in the same location, You could have `.git/` deeper into your Main Working Tree. When doing this, things can get a bit confusing.  

__TL;DR__ The position root of your Git Repository will reposition to the parent directory if you run Git from the parent directory. If you have:

    ~/parentdir/
        .git/
        .gitignore
        parentcode.js

        subdir/
            .git/
            .gitignore
            subcode.js

1. Run this:  
    `cd ~/parentdir/subdir; git add # ...etc`  
    and the repository root is `subdir/`  
2. Then run this:  
    `cd ~/parentdir; git --git-dir=subdir/.git add # ...etc`  
    and repository root moves up `parentdir/`!  
3. You get the same result as 1. if you run:  
    `cd ~/parentdir; git --work-tree=subtree --git-dir=subdir/.git add # ...etc`  
4. `--git-dir` by itself does not equate `cd`ing there to run git commands  
    which means the working tree is interpreted as wherever you are in shell.  
5. Adding `--work-tree` sort of simulates `cd`ing there to run git commands,  
    causing git to consider this location the "work-tree"  
6. If you `cd`, you don't need `--git-dir` unless you renamed `.git`  
7. If you `cd`, the "work-tree" is where now are unless you use `--word-dir`  
8. `--word-tree` + `--git-dir` is the equivalent to `cd` but the following:  
    `cd ~/parentdir; git -C add #...etc`
    has `-C` forces git to `cd` first and is the newer way to do 1. or 3.

#### The Long Explanation/Demo

Do this:  

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

So let's say you have a directory full of different repositories and you want to work from their common parent directory, which is not directly tracked by git. Let's look at an example like that but for simplicity, only one repository in it.  

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

## Multiple Git Techniques for Jeffpack

__Method 1: All Have Same Repository Root, Housing Multiple `.git/` Name Variants__  

If you want multiple Git repositories for the same code, all centered at the same directory level you can simply things a lot by just using multiple names for `.git/`, all in this same directory. You can toggle between with `--git-dir=.git-another`, for example, without needing `--work-dir` or `-C`.  

You could for example have `.git-backup` which doesn't ignore anything and pushes to a different (probably private) repository to back up a particular site in it's entirety.  

The defining difference between each Git Repository would be different ignore / don't ignore rules for each. If you choose this way _you need to know_ that only the ignore rules found in `.git/info/exclude` will be dedicated to each Git and if you use `.gitignore`, whatever is set there will be used by all of them. If you specify your ignores (or whitelisting) separately in each `.git/info/exclude` instead of a `.gitignore` file at the root you can have a fresh set of rules for each. You'd probably need to do this.  

__Method 2: All Have Same Repository Root But Each `.git/`s In Different Subdir__  

If you have a file structure like this:  

    parentdir/
        just-jeffpack/
            .git/
            .gitignore
        entire-site/
            .git/
        ...
        wp-content/
        ...

You could have a whitelist in `just-jeffpack/.git` and not ignore anything in `entire-site/.git`. You would of course have to modify your Jeffpack itself with as well as do some other setup:  

    mkdir just-jeffpack entire-site
    git mv .git just-jetpack/.git
    git mv .gitignore just-jetpack/.gitignore
    git --git-dir=entire-site/.git init

Then you would add all and commit for both.  

__Method 3: Different Everything - DIY Submodules__  

If your own code is really just contained within `wp-content` you could move jetpack to be there and then track the entire site one level up.  To do this you probably to start over with a new .git for jeffpack:  

    # cd to jeffpack which is also the wordpress site's root
    rm -rf .git
    mv .gitignore wp-content/.gitignore
    git init
    git -C wp-content init

Then just add everything and commit. To push the jeffpack part you'll need to add the remote back, obviously.  
