# SuperSaaS Online Appointment Scheduling -- WordPress Plugin

The SuperSaaS WordPress plugin displays a "Book now" button that automatically logs the user into a SuperSaaS schedule using his WordPress user name. It passes the user's information along, creating or updating the user's information on SuperSaaS as needed.

Note that you will need to configure both the WordPress plugin *and* your SuperSaaS account. Please read the setup instructions at:

<http://www.supersaas.com/info/doc/integration/wordpress_integration>

Once installed you can add a button to your pages by placing the *supersaas* shortcode in the text of a WordPress article:

* Default button example:
```
[supersaas]
```
* A custom button example:
```
[supersaas after=booking_system label="Book Here!" image='http://link.to/background.png']
```

The shortcode takes the following optional arguments.

* `after` - The name of the schedule or an URL. Defaults to the schedule configured on the WordPress Admin page at the settings section for SuperSaaS. Entering a schedule name at the SuperSaaS settings section is optional.
* `label` - The button label. This defaults to “Book Now” or its equivalent in the supported languages. If the button has a background image, this will be the *alternate* text value.
* `image` - The URL of the background image. This has no default value. So, the button will not have a background image, if this isn’t configured.

For further details of the SuperSaaS WordPress plugin see also the *readme.txt* file.
