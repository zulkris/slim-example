<?php

namespace App;

class Validator implements ValidatorInterface
{
    public function validate(array $course)
    {
        // BEGIN (write your solution here)
        $errors = [];
        if ((int)$course['paid'] > 1 || (int)$course['paid'] < 0) {
            $errors['paid'] = 'Can\'t be blank';
        }
        if (empty($course['title'])) {
            $errors['title'] = 'Can\'t be blank';
        }

        return $errors;
        // END
    }
}
