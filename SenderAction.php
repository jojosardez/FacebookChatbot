<?php

/**
 * Class SenderAction
 * This class serves as an enum for the different types of sender actions.
 *
 * @author: Angelito Sardez, Jr.
 * @date: 12/11/2017
 */
abstract class SenderAction {
    const markSeen = 0;
    const typingOn = 1;
    const typingOff = 2;
}