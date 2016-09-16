[![StyleCI](https://styleci.io/repos/63356264/shield)](https://styleci.io/repos/63356264)
[![Build Status](https://travis-ci.org/RMoorePHP/change-recorder.svg)](https://travis-ci.org/RMoorePHP/change-recorder)

Records changes made to eloquent models (such as update/create/delete) and provides methods to check a models history

to install
`composer require rmoore/change-recorder`

add `RMoore\ChangeRecorder\ChangeRecorderServiceProvider::class` to the providers array in config\app.php

run `artisan migrate` to create the database table

the add the trait `RMoore\ChangeRecorder\RecordsChanges` to your eloquent models.

this will now automatically record changes made

to view changes we have a few choices

1st we can call `$model->changes` which will return a collection of Change instances containing all the data that was stored about each change

2nd we can call `$model->getHistory()` which will return the same results as option 1


if you wish to find specific changes, you can pass the field name as a parameter to getHistory, or you can use magic methods. please note these will only return changes where this was the only field changed at that time. if you wish to perform a more in depth search please continue reading

1st we can call get history and pass the field we want as an arguement, eg `$model->getHistory('name')` will return the users name history

2th we can use magic methods which take the form of get{fieldName}History(), eg `$model->getNameHistory()` which will return the same results as option 3


for in depth searching (aka finding changes where other fields were changed at the same time), you can use the searchHistory methods.

1st option is to pass the field as an argument, eg `$model->searchHistory('name')`
 
2nd option is to use magic methods, eg `$model->searchNameHistory()`

3rd option is to pass a boolean as the 2nd parameter to getHistory, eg `$model->getHistory('name', true)` will return the same results as options 1 and 2
