# SmartCounter

Simple running tally of the number of visits a webpage has received using JSON.
Good for file-flat CMS.




## Usage

Specify the directory for storing data by changing the property __statisticsFilePath__ in `SmartCounter` class

```$statisticsFilePath = 'user/smartcounter.json';```

Define a Class

```
require 'src/smartCounter.php';

$counter = new FriendlyWeb\SmartCounter();
```

To count visitors use __count()__ method

```$counter->count();```

To output statistics use __rawStats()__ method

```$counter->rawStats();```

## Examples

Simple example of output data with __rawStats()__ method

```
{
   "date":"2022-05-26",
   "common":{
      "hits":[
         11,
         0,
         12
      ],
      "hosts":[
         1,
         0,
         1
      ]
   }
}
```

## Idea

I only need to know how many hosts and hits visit the site each day. Most website traffic counters store a large amount of data (including IP, exact time including milliseconds, browser, etc.). I was only interested in the sequence of visits per day. 

SmartCounter counts website traffic (hits and hosts) using a __sequence of days__. The sequence is written from the date which is stored in the JSON file.

## Cookie

SmartCounter uses cookies to identify a new site visitor. Only the date of the last visit is stored.

```smartcounter:"2022-05-27"```
