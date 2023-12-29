<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Message Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during Message for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'validation' => 'Validation Failed',
    'error' => 'Something went wrong, please try again later',

    'jwt' => [
        'missing' => 'Token is missing',
        'invalid' => 'Invalid token',
        'expired' => 'Token expired',
        'not-found' => 'Token not found',
        'unauthorized' => 'Unauthorized Access',
    ],

    'list' => [
        'success' => 'List Fetched Successfully',
        'failed'  => 'No Data Found',
        'no_content' => 'No Content Found',
    ],

    'add' => [
        'success' => 'Details Added Successfully',
        'failed'  => 'Add Failed',
    ],

    'delete' => [
        'success' => 'Record Deleted Successfully',
        'failed'  => 'Delete Failed',
        'not-found' => ':entity not found',
    ],

    'update' => [
        'success' => 'Details Updated Successfully',
        'failed'  => 'Update Failed',
        'not-found' => ':entity not found',
    ],

    'detail' => [
        'success' => 'Details Fetched Successfully',
        'failed'  => 'Details Not Found',
        'not-found' => ':entity not found',
        'inactive' => 'Your account has been blocked by the administrator',
    ], 

    'login' => [
        'success' => 'Login Successful',
        'failed'  => 'Login Failed',
        'not-found' => 'User Not Found, Please Register First...',
        'invalid' => 'Password Does Not Match!',
        'inactive' => 'Your account has been blocked by the administrator',
        'not-verified' => 'Email not Verified, please verify it first...',
        'invalid-email' => 'Invalid Email Address',
    ],

    'change-password' => [
        'success' => 'Password Updated Successfully',
        'failed'  => 'Unable to update Password, please try again...',
        'not-found' => 'User Not Found, Please Register First...',
        'invalid' => 'Old Password Does Not Match!',
        'inactive' => 'Your account has been blocked by the administrator',
        'not-verified' => 'Email not Verified, please verify it first...'
    ],

    'change-status' => [
        'success' => 'Status Updated Successfully',
        'failed'  => 'Unable to update status, please try again...',
        'not-found' => ':entity not found',        
        'inactive' => 'Your account has been blocked by the administrator',
    ],

    'reset-password' => [
        'success' => 'Password Reset Successfully',
        'failed'  => 'Unable to reset Password, please try again...',
        'not-found' => ':entity not found',
    ], 

    'change-stage' => [
        'success' => 'Stage Changed Successfully',
        'failed'  => 'Unable to change stage, please try again...',
        'not-found' => ':entity not found',
    ],

    'download' => [
        'success' => 'File Downloaded Successfully',
        'failed'  => 'Unable to download file, please try again...',
    ],
];
