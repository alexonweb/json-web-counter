# SmartCounter

Simple running tally of the number of visits a webpage has received using JSON.
Good for file-flat CMS.

## Usage

Specify the directory for storing data by changing the property __statisticsFilePath__ in `SmartCounter` class

```$statisticsFilePath = 'user/smartcounter.json';```

Define a Class

```
require 'src/SmartCounter.php';

$counter = new FriendlyWeb\SmartCounter();
```

To count visitors use __count()__ method

```$counter->count();```

Output total views of all pages use __views()__ method

```$counter->views()```

of current page use param __views(true)__

```$counter->views(true)```

Output visits of all pages use __visits()__ method 

```$counter->visits()```

same as for __views()__ use param for current page

```$counter->visits(true)```

To output current statistics data in JSON use __rawStats()__ method

```$counter->rawStats();```

## Examples

Simple example of output data with __rawStats()__ method

```
{
    "date":"2022-06-17",
    "pages":[
        {
            "uri":"index",
            "hits":[
                0,
                0,
                0,
                0
            ],
            "hosts":[
                0,
                0,
                0,
                0
            ],
            "unique":0
        },
        {
            "uri":"examples.php",
            "hits":[
                13,
                0,
                0,
                1
            ],
            "hosts":[
                1,
                0,
                0,
                0
            ],
            "unique":1
        }
    ]
}
```


## Idea

SmartCounter counts website traffic (hits and hosts) using a __sequence of days__. The sequence is written from the date which is stored in the JSON file. Most website traffic counters store a large amount of data (including IP, exact time including milliseconds, browser, etc.). SmartCounter only store sequence of visits per day by the URI (URL).

## Cookie

SmartCounter uses cookies to identify a new site visitor. Only the date of the last visit is stored.

```smartcounter:"2022-05-27"```