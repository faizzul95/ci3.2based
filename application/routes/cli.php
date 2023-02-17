<?php

/**
 * CLI Routes
 *
 * This routes only will be available under a CLI environment
 */

// To enable Luthier-CI built-in cli commands
// uncomment the followings lines:

Luthier\Cli::maker();
Luthier\Cli::migrations();

Route::cli('jobs', 'JobController@work');

Route::cli('jobs/listen', 'JobController@listen');
Route::cli('jobs/launch', 'JobController@launch');
Route::cli('jobs/work', 'JobController@work');
Route::cli('jobs/single', 'JobController@single');
