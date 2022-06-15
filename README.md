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

To output statistics use __rawStats()__ method

```$counter->rawStats();```

## Examples

Simple example of output data with __rawStats()__ method

```
{
	"date":"2022-06-15",
	"pages":[
		{
			"uri":"index",
			"hits":[
				0,
				0,
            0
			],
			"hosts":[
				0,
				0,
            0
			]
		},
		{
			"uri":"examples.php",
			"hits":[
				5,
				3,
            1
			],
			"hosts":[
				0,
				1,
            2
			]
		}
	]
}
```

## Idea

SmartCounter counts website traffic (hits and hosts) using a __sequence of days__. The sequence is written from the date which is stored in the JSON file. Most website traffic counters store a large amount of data (including IP, exact time including milliseconds, browser, etc.). SmartCounter only store sequence of visits per day by the URI (URL).

## Cookie

SmartCounter uses cookies to identify a new site visitor. Only the date of the last visit is stored.

```smartcounter:"2022-05-27"```
