<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validierungs-Sprachzeilen
    |--------------------------------------------------------------------------
    |
    | Die folgenden Sprachzeilen enthalten die Standardfehlermeldungen, die von
    | der Validator-Klasse verwendet werden. Einige dieser Regeln haben
    | mehrere Versionen, wie z.B. die Größenregeln. Fühlen Sie sich frei,
    | jede dieser Nachrichten hier anzupassen.
    |
    */

    'accepted' => 'Das :attribute muss akzeptiert werden.',
    'active_url' => 'Das :attribute ist keine gültige URL.',
    'after' => 'Das :attribute muss ein Datum nach dem :date sein.',
    'alpha' => 'Das :attribute darf nur Buchstaben enthalten.',
    'alpha_dash' => 'Das :attribute darf nur Buchstaben, Zahlen und Bindestriche enthalten.',
    'alpha_num' => 'Das :attribute darf nur Buchstaben und Zahlen enthalten.',
    'array' => 'Das :attribute muss ein Array sein.',
    'before' => 'Das :attribute muss ein Datum vor dem :date sein.',
    'between' => [
        'numeric' => 'Das :attribute muss zwischen :min und :max liegen.',
        'file' => 'Das :attribute muss zwischen :min und :max Kilobytes groß sein.',
        'string' => 'Das :attribute muss zwischen :min und :max Zeichen lang sein.',
        'array' => 'Das :attribute muss zwischen :min und :max Elemente haben.',
    ],
    'boolean' => 'Das :attribute Feld muss wahr oder falsch sein.',
    'confirmed' => 'Die :attribute Bestätigung stimmt nicht überein.',
    'date' => 'Das :attribute ist kein gültiges Datum.',
    'date_format' => 'Das :attribute entspricht nicht dem Format :format.',
    'different' => 'Das :attribute und :other müssen unterschiedlich sein.',
    'digits' => 'Das :attribute muss :digits Ziffern haben.',
    'digits_between' => 'Das :attribute muss zwischen :min und :max Ziffern liegen.',
    'email' => 'Das :attribute muss eine gültige E-Mail-Adresse sein.',
    'filled' => 'Das :attribute Feld ist erforderlich.',
    'exists' => 'Das ausgewählte :attribute ist ungültig.',
    'image' => 'Das :attribute muss ein Bild sein.',
    'in' => 'Das ausgewählte :attribute ist ungültig.',
    'integer' => 'Das :attribute muss eine ganze Zahl sein.',
    'ip' => 'Das :attribute muss eine gültige IP-Adresse sein.',
    'max' => [
        'numeric' => 'Das :attribute darf nicht größer als :max sein.',
        'file' => 'Das :attribute darf nicht größer als :max Kilobytes sein.',
        'string' => 'Das :attribute darf nicht mehr als :max Zeichen haben.',
        'array' => 'Das :attribute darf nicht mehr als :max Elemente haben.',
    ],
    'mimes' => 'Das :attribute muss eine Datei des Typs: :values sein.',
    'min' => [
        'numeric' => 'Das :attribute muss mindestens :min sein.',
        'file' => 'Das :attribute muss mindestens :min Kilobytes groß sein.',
        'string' => 'Das :attribute muss mindestens :min Zeichen lang sein.',
        'array' => 'Das :attribute muss mindestens :min Elemente haben.',
    ],
    'not_in' => 'Das ausgewählte :attribute ist ungültig.',
    'numeric' => 'Das :attribute muss eine Zahl sein.',
    'regex' => 'Das :attribute Format ist ungültig.',
    'required' => 'Das :attribute Feld ist erforderlich.',
    'required_if' => 'Das :attribute Feld ist erforderlich, wenn :other :value ist.',
    'required_with' => 'Das :attribute Feld ist erforderlich, wenn :values vorhanden ist.',
    'required_with_all' => 'Das :attribute Feld ist erforderlich, wenn :values vorhanden sind.',
    'required_without' => 'Das :attribute Feld ist erforderlich, wenn :values nicht vorhanden ist.',
    'required_without_all' => 'Das :attribute Feld ist erforderlich, wenn keines von :values vorhanden ist.',
    'same' => 'Das :attribute und :other müssen übereinstimmen.',
    'size' => [
        'numeric' => 'Das :attribute muss :size sein.',
        'file' => 'Das :attribute muss :size Kilobytes sein.',
        'string' => 'Das :attribute muss :size Zeichen lang sein.',
        'array' => 'Das :attribute muss :size Elemente enthalten.',
    ],
    'timezone' => 'Das :attribute muss eine gültige Zone sein.',
    'unique' => 'Das :attribute wurde bereits vergeben.',
    'url' => 'Das :attribute Format ist ungültig.',
    'casys' => 'Alle casys Felder sind nicht vorhanden',

    /*
    |--------------------------------------------------------------------------
    | Benutzerdefinierte Validierungs-Sprachzeilen
    |--------------------------------------------------------------------------
    |
    | Hier können Sie benutzerdefinierte Validierungsnachrichten für Attribute
    | mit der Konvention "attribute.rule" angeben, um die Zeilen zu benennen.
    | Dies ermöglicht es schnell, eine spezifische benutzerdefinierte Sprachzeile
    | für eine gegebene Attributregel anzugeben.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'benutzerdefinierte Nachricht',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Benutzerdefinierte Validierungsattribute
    |--------------------------------------------------------------------------
    |
    | Die folgenden Sprachzeilen werden verwendet, um Attributplatzhalter
    | mit etwas leserfreundlicherem zu ersetzen, wie z.B. E-Mail-Adresse
    | anstatt "email". Dies hilft uns, Nachrichten etwas klarer zu machen.
    |
    */

    'attributes' => [],

];
