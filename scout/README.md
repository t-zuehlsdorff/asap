# Scout
## Introduction

The scout has the honor to scout, whether the given port has an update available or not.

## FreeBSD ports tree (fun facts)

0. Used data is from 2016-01-16
1. Number of found ports: 25.643
2. Number of maintainer: 1.552 - 1 :)
3. Number of ports by maintainer

```postgresql
         mail         |  count
----------------------+-------
 ports@freebsd.org    | 4644
 perl@freebsd.org     | 2271
 sunpoet@freebsd.org  |  944
 ruby@freebsd.org     |  910
 kde@freebsd.org      |  586
 gnome@freebsd.org    |  538
 bofh@freebsd.org     |  497
 haskell@freebsd.org  |  490
 kuriyama@freebsd.org |  480
 office@freebsd.org   |  320
````

4. Number of ports by maintainer and category

```postgresql
        mail         | portcategory |  count
---------------------+--------------+-------
 perl@freebsd.org    | devel        | 754
 ports@freebsd.org   | games        | 546
 ports@freebsd.org   | devel        | 424
 sunpoet@freebsd.org | devel        | 360
 ports@freebsd.org   | www          | 357
 perl@freebsd.org    | www          | 348
 ruby@freebsd.org    | devel        | 345
 perl@freebsd.org    | textproc     | 295
 ports@freebsd.org   | audio        | 281
 ports@freebsd.org   | graphics     | 255
````

5. Most frequent versions

```postgresql
 currentversion | count 
----------------+-------
 1.0            |   461
 1.1            |   246
 4.14.3         |   237
 1.2            |   190
 1.0.1          |   174
 0.02           |   173
 1.3            |   172
 0.03           |   164
 0.1            |   157
 0.04           |   157
````

6. 22.554 (~87,95%) ports are up to date and 3.089 are outdated (~12,05%)

## Version comparison

the idea is to look over the given port versions and to find the different used version formats.
let's start with the most obvious format. I think it should be something like a number, maybe one or two digits, (if) followed
normaly by a dot, maybe again 1-3 digits, maybe again a dot followed by 1-3 digits.

```postgresql
SELECT COUNT(currentversion) FROM versions WHERE currentversion ~ '^[\d]{1,3}(\.\d{1,3}){0,2}$';

 count 
-------
 22044
(1 Zeile)
````

Okay, this pattern would handle 85,96 % of ports. Okay, only 14,04 % to go.