# Documentation
For details on PSR-2-R see [fig-rectified-standards](https://github.com/php-fig-rectified/fig-rectified-standards).

## Documentation on the fixer itself
This uses and extends [FriendsOfPHP/PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer).

## Writing fixers
We write sniffers mainly for the above standard (using tabs), but all fixers should also aim to work with spaces as indentation.

Basic principles:

- Fixers only change what is absolutely necessary (no side effects).
- Fixers aim to be non-risky (meaning of code / functional behavior should not be changed) or need to be explicitly documented as such.
- The naming should make it clear what the fixer does. If there is a negation of the fixer that name must also be taken into consideration (SpaceAfterCast and NoSpaceAfterCast for example).

## Open Tasks
It would be nice if some of these fixers find their way into the contrib section of the original fixer.
If anyone wants to contribute and add those there, that would be awesome.
