<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="shortcut icon"  href={{ URL::asset('images/fav_icon.png') }} type="image/png" />

	<title>@if(isset($title)){{$title}}@endif</title>
<!-- CSS Files -->
<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/jqueryui.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrapValidator.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/font-awesome/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">

<!-- JS Files -->
<script src="{{ asset('script/jquery.js') }}"></script>
<script src="{{ asset('script/jqueryui.js') }}"></script>
<script src="{{ asset('script/bootstrap.min.js') }}"></script>
<script src="{{ asset('script/bootstrapValidator.min.js') }}"></script>
<script src="{{ asset('script/sweetalert.min.js') }}"></script>




@if(Auth::user()->id == 1)
    <link rel="stylesheet" href="{{ asset('css/admin-common.css') }}">
    <script src="{{ asset('script/admin-common.js') }}"></script>
@endif

@if(Auth::user()->id > 1)
    <link rel="stylesheet" href="{{ asset('css/user-common.css') }}">
    <script src="{{ asset('script/user-common.js') }}"></script>
@endif


</head>
<body>

@if(Auth::check())


<!-- Admin Section -->

@if(Auth::user()->id==1)

@include('layouts.header_admin')
@yield('content')
@include('layouts.footer_admin')


@endif




<!-- User Section -->

@if(Auth::user()->id>1)

@include('layouts.header_user')
@yield('content')
@include('layouts.footer_user')

@endif







@endif

<script>

// Remove HTML tags + unwanted symbols
function stripTagsAndSymbols(value) {
    // 1) Remove HTML tags
    let clean = $('<div>').html(value).text();

    // 2) Remove unwanted symbols (allow only letters, numbers, space, dot, comma, hyphen)
    clean = clean.replace(/[^a-zA-Z0-9\s.,-]/g, '');

    return clean;
}

function sanitizeInput(el) {
    el.value = stripTagsAndSymbols(el.value);
}
function sanitizeEmailInput(el) {

    let value = el.value;


    // 1) Remove all characters except letters, numbers, @, ., _, -
    value = value.replace(/[^a-zA-Z0-9@._-]/g, '');

    // 2) Allow only one @
    const parts = value.split('@');
    if (parts.length > 2) {
        value = parts[0] + '@' + parts.slice(1).join('');
    }

    // 3) Remove consecutive dots
    value = value.replace(/\.{2,}/g, '.');

    // 4) Remove dot at start
    value = value.replace(/^\./, '');

    // 5) Remove dot before @
    value = value.replace(/\.(?=@)/, '');

    el.value = value;
}
// Sanitize multiple emails input
function sanitizeMultipleEmails(el) {
    let emails = el.value.split(','); // split by comma
    emails = emails.map(email => {
        // Remove unwanted characters except valid email chars
        email = email.replace(/[^a-zA-Z0-9@._-]/g, '');

        // Ensure only one @ per email
        const parts = email.split('@');
        if (parts.length > 2) {
            email = parts[0] + '@' + parts.slice(1).join('');
        }

        // Remove consecutive dots
        email = email.replace(/\.{2,}/g, '.');

        // Remove dot at start or before @
        email = email.replace(/^\./, '').replace(/\.(?=@)/, '');

        return email.trim();
    });

    // Join back with comma
    el.value = emails.join(',');
}

// Validate multiple emails input
function validateMultipleEmails(input) {
    const emails = input.split(',').map(e => e.trim());
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    for (let email of emails) {
        if (email && !regex.test(email)) {
            return false; // invalid email found
        }
    }
    return true; // all emails valid
}

</script>
</body>
</html>

