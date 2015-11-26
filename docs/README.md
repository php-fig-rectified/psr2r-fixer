# Documentation
For details on PSR-2-R see [fig-rectified-standards](https://github.com/php-fig-rectified/fig-rectified-standards).

## Documentation on the fixer itself
This uses and extends [FriendsOfPHP/PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer).

## Fixers available
The following fixers are bundles together with PSR-2-R already, but you can
also use them standalone/separately in any way you like.

### PSR-2-R
- UseTabs
- ConsistentBraces (Always on the end of the same line)
- FunctionSpacing (Newlines above and below each function/method)
- EmptyEnclosingLines (Newline at beginning and end of class)

### Additions
- NoInlineAssignment
- ConditionalExpressionOrder
- MethodArgumentDefaultValue
- RemoveFunctionAlias
- ShortCast
- NoSpacesCast
- NoIsNull
- PreferCastOverFunction
- WhitespaceAfterReturn
- PhpSapiConstant
- PhpdocParams
- PhpdocPipe
- PhpdocReturnSelf

## Important notes
- Do not run the fixer with any path after the command "fix". It will then ignore your .phpcs file completely.
Better to copy your .phpcs file into the directory you want to specifically fix and run the fixer relatively from there.
The other option would be to temporary modify the path set in .phpcs.

## Writing fixers
We write sniffers mainly for the above standard (using tabs), but all fixers should also aim to work with spaces as indentation.

Basic principles:

- Fixers only change what is absolutely necessary (no side effects).
- Fixers aim to be non-risky (meaning of code / functional behavior should not be changed) or need to be explicitly documented as such.
- The naming should make it clear what the fixer does. If there is a negation of the fixer that name must also be taken into consideration (SpaceAfterCast and NoSpaceAfterCast for example).

## Open Tasks
It would be nice if some of these fixers find their way into the contrib section of the original fixer.
If anyone wants to contribute and add those there, that would be awesome.
