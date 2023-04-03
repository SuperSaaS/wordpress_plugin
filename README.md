# SuperSaaS Online Appointment Scheduling -- WordPress Plugin

The SuperSaaS WordPress plugin displays a "Book now" button that automatically logs the user into a SuperSaaS schedule using his WordPress username. It passes the user's information along, creating or updating the user's information on SuperSaaS as needed.

Note that you will need to configure both the WordPress plugin *and* your SuperSaaS account. Please read the setup instructions at:

<http://www.supersaas.com/info/doc/integration/wordpress_integration>


Once installed you can add a button to your pages by placing the `supersaas` shortcode in the text of a WordPress article:

* Default button example:
```
[supersaas]
```
* A custom button example:
```
[supersaas schedule=schedule_name label="Book Here!" image='https://cdn.supersaas.net/en/but/book_now_red.png']
```
* A custom button example that opens a widget for `schedule_name` displayed as cards with menu at the top:
```
[supersaas schedule=schedule_name label="Pick a time!" view="card" menu_pos="top"]
```

The shortcode takes the following optional arguments.
* `schedule` - The name of the schedule or a URL. Defaults to the schedule configured on the WordPress Admin page at the settings section for SuperSaaS. Entering a schedule name at the SuperSaaS settings section is optional.
* `label` - The button label. This defaults to “Book Now” or its equivalent in the supported languages. If the button has a background image, this will be the *alternate* text value.
* `image` - The URL of the background image. This has no default value. So, the button will not have a background image, if this isn’t configured.
> **_NOTE:_** `image` attribute is ignored when the widget display options is selected in plugin settings. In that case button look and feel can be configured on [widget builder](https://www.supersaas.com/info/doc/integration/integration_with_widget) page

If you selected 'Show a SuperSaaS widget containing the calendar as a button or a frame directly on my site' in the plugin settings you can provide additional attributes to the shortcode:
* `options` - The JSON encoded into a string that contains overrides to widget configuration. For example `options="{'view':'card','menu_pos':'top'}"`. More configuration options can be found on [widget builder](https://www.supersaas.com/info/doc/integration/integration_with_widget) page.
* _Any_ other arguments provided to shortcode will be treated as an override to widget options. For example `[supersaas view="card"]` will provide `"view":"card"` to widget configuration options. 

For further details of the SuperSaaS WordPress plugin see also the **readme.txt** file.
