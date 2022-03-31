<html>
<head>
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    {!! JsonLd::generate() !!}
    // OR with multi
    {!! JsonLdMulti::generate() !!}

    <!-- OR -->
    {!! SEO::generate() !!}

    <!-- MINIFIED -->
    {!! SEO::generate(true) !!}

</head>
<body>
 <h3>Laravel 8 SEO Tutorial</h3>
</body>
</html>