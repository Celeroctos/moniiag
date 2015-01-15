<?php

/**
 * Class LError - Default error exception
 */
class LError extends Exception {
}

/**
 * Class LNoSuchUserException - Exception will be thrown if user doesn't exist
 */
class LNoSuchUserException extends Exception {
}

/**
 * Class LAccessDeniedException - Exception will be thrown if access denied
 * to provide some action
 */
class LAccessDeniedException extends Exception {
}