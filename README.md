## Usage

#### Creating a Month instance

```php
use Dzava\CalendarMonth\Month;

new Month(); // current month
new Month(5); // may of current year
new Month(5, 2019); // may of 2019
```

#### Retrieving the days in a month

To get an array of the days in a month use the `days()` method.
```php
use Dzava\CalendarMonth\Month;

$month = new Month(5, 2018);

$days = $month->days();

// array(31) {
//    [0] => class Carbon\Carbon {} // 2018-05-01
//    ...
//    [30] => class Carbon\Carbon {} // 2018-05-31
// }
```

To get a list of days per week of the month use the `weeks()` method.
```php
use Dzava\CalendarMonth\Month;

$month = new Month(5, 2018);

$weeks = $month->weeks();

// array(5) {
//    [0] => array(7) {
//        [0] => class Carbon\Carbon {} // 2018-04-29
//        ...
//        [6] => class Carbon\Carbon {} // 2018-05-05
//    }
//    ...
//    [4] => array(7) {} // 2018-05-27 - 2018-06-02
// }
```

By default the weeks start on Sunday. If you want your weeks to start on a different day, use the `weekStartsAt($day)` method. 0 (for Sunday) through 6 (for Saturday).

#### Checks

To check if a given date belongs to the month use the `contains($day)` method. The day can be either a `Carbon` instance or in any [other format](https://carbon.nesbot.com/docs/#api-instantiation) that is supported by the `Carbon` constructor.

```php
use Dzava\CalendarMonth\Month;

$month = new Month(5, 2018);

$month->contains('2018-05-02'); // true
$month->contains('2018-04-02'); // false
```
