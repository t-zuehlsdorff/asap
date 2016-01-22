# Scout
## Introduction

The scout has the honor to scout, whether the given port has an update available or not.

## FreeBSD ports tree (fun facts)

0. Used data is from 2016-01-16 and from [portscout](http://portscout.freebsd.org)
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
```

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
```

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
```

6. 22.554 (~87,95%) ports are up to date and 3.089 are outdated (~12,05%)

## Version comparison

the idea is to look over the given port versions and to find the different used version formats.
let's start with the most obvious format. I think it should be something like a number, maybe one or two digits, (if) followed
normaly by a dot, maybe again 1-3 digits, maybe again a dot followed by 1-3 digits.

```postgresql
asap=# SELECT COUNT(currentversion) FROM versions WHERE currentversion ~ '^[\d]{1,3}(\.\d{1,3}){0,2}$';

 count 
-------
 22044
(1 Zeile)
```

Okay, this pattern would handle 85,96 % of ports.
As I took a closer look what the remaining versions look like, I noticed many versions in the
format x.x.x.x, so I decided to adjust the regex.

 ```postgresql
asap=# SELECT COUNT(*) FROM versions WHERE currentversion ~ '^[\d]+(\.\d+)*$';

 count 
-------
 24215
(1 Zeile)
```

Huh, that is 94,43%!
Okay, now we drop those data to see what's left.

```postgresql
asap=# SELECT currentversion FROM versions LIMIT 25;

  currentversion   
-------------------
 1.0p8
 .10
 r1
 2-31-0
 0.999b
 2.0b4
 211a
 6L38
 0.7beta
 1.5.8c
 v13.11.08
 v13.11.08
 1_5_5
 6-08
 0.1e
 0.997a
 1.2.2.s2015012200
 r3598
 r423
 git20130522
 r475
 svn20130912
 svn20130912
 git20140423
 0.5-0
(25 Zeilen)

and "sorted":

```postgresql
asap=# SELECT currentversion FROM versions ORDER BY currentversion DESC LIMIT 25;

  currentversion   
-------------------
 v2.4c
 v1_7_21229
 v13.11.08
 v13.11.08
 v1.2.0
 v1.1.3
 v1.03
 v1.0.3
 v1.0.2-beta1_i386
 v0.9
 v0.5.1
 v0.01
 svn20130912
 svn20130912
 rfc3951
 rc6
 r8765
 r769
 r7421
 r6440
 r6329
 r63
 r6
 r6
 r475
(25 Zeilen)

Okay, we can easily parse the versions with the "v" prefix.

```postgresql
asap=# SELECT COUNT(*) FROM versions WHERE currentversion ~ '^v[\d]+(\.\d+)*$';

 count 
-------
     9
(1 Zeile)

Damn, only 9 matches :(


tl;dr

|covers|regex|
|---|---|
|24.215 (~94,43 %)|^[\d]+(\.\d+)*$|
|286 (~1,12 %)|^\d+(\.\d+)+[a-z]$|
|124 (~0,48%)|^[\d]{1,3}(_\d{1,3})+$|
|122 (~0,48%)|^\d+(\.\d+)+-\d{1,4}$|
|76 (~0,3%)|^\d+(\.\d+)[a-z]\d$|


820 not supported.

note: first regex that covers the most, will also catch pure versions that are dates.
this is not what we want.
