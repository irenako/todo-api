<?php

namespace App;

enum TaskStatus: string
{
    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case FINISHED = 'finished';
    case FAILED = 'failed';
}
