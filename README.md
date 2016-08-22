[![StyleCI](https://styleci.io/repos/63356264/shield)](https://styleci.io/repos/63356264)

Records changes made to eloquent models (such as update/create/delete) and provides methods to check a models history

to install
`composer require rmoore/change-recorder`

add `RMoore\ChangeRecorder\ChangeRecorderServiceProvider::class` to the providers array in config\app.php

run `artisan vendor:publish` and then `artisan migrate` to create the database table

the add the trait `RMoore\ChangeRecorder\RecordsChanges` to your eloquent models.

this will now automatically record changes made

to view changes we have a few choices

1st we can call `$model->changes` which will return a collection of Change instances containing all the data that was stored about each change

2nd we can call `$model->getHistory()` which will return an array of formatted changes containing the date, before and after

3rd we can call get history and pass the field we want as an arguement, eg `$model->getHistory('name')` will return the users name history

4th we can use magic methods which take the form of get{fieldName}History(), eg `$model->getNameHistory()` which will return the same results as option 3

note: methods 3 and 4 will only return results where the only change made was that field, if you want to include results where multiple fields were changed
at the same time, you will have to use options 1 or 2 and parse them yourself for now.
