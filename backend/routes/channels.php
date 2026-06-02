<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('showtime.{showtimeId}.seats', fn () => true);
