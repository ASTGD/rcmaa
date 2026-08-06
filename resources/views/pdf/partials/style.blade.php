{{--
    Shared styling for the PDF slips.

    dompdf understands a narrow slice of CSS — no flexbox, no grid, no custom
    properties — so this is deliberately tables-and-blocks, with colours written
    out in full. It has nothing to do with the site's stylesheet, and should not
    be made to share one.
--}}
<style>
    /* Tight enough that a full registration — every optional field filled — still
       lands on one page. People print this and carry it to the desk, and the
       closing note spilling onto a second sheet on its own is worse than small
       margins. */
    @page { margin: 15mm 14mm 13mm; }

    body {
        font-family: DejaVu Sans, sans-serif;   /* the one bundled font with wide coverage */
        font-size: 10.5px;
        line-height: 1.5;
        color: #1c2434;
    }

    .head { border-bottom: 2px solid #c8a863; padding-bottom: 9px; margin-bottom: 14px; }
    .head td { vertical-align: middle; }
    .logo { width: 54px; }
    .org { font-size: 15px; font-weight: bold; color: #070e1b; }
    .org small { display: block; font-size: 8.5px; font-weight: normal; letter-spacing: 2px; color: #8a6d2f; text-transform: uppercase; }
    .doc { text-align: right; }
    .doc .kind { font-size: 12px; font-weight: bold; color: #070e1b; }
    .doc .ref { font-size: 15px; letter-spacing: 1px; color: #8a6d2f; font-weight: bold; }

    h2 {
        font-size: 9px; text-transform: uppercase; letter-spacing: 1.6px;
        color: #6b7688; margin: 13px 0 5px; padding-bottom: 3px;
        border-bottom: 1px solid #e3e0d8;
    }

    table.data { width: 100%; border-collapse: collapse; }
    table.data td { padding: 3.5px 0; vertical-align: top; border-bottom: 1px solid #f0eee8; }
    table.data td.k { color: #6b7688; width: 38%; }
    table.data td.v { color: #10182a; font-weight: bold; }

    .chip { display: inline-block; padding: 3px 9px; border-radius: 9px; font-size: 9px; font-weight: bold; }
    .chip-ok   { background: #d9f2e3; color: #17663c; }
    .chip-wait { background: #fdf0d2; color: #8a5d0d; }
    .chip-no   { background: #fadcdc; color: #8f2020; }

    .total { background: #f7f5ef; border: 1px solid #e3e0d8; padding: 10px 12px; margin-top: 10px; }
    .total .amt { font-size: 17px; font-weight: bold; color: #070e1b; }

    .note { background: #f7f5ef; border-left: 3px solid #c8a863; padding: 7px 10px; margin-top: 12px; font-size: 9.5px; color: #4a5468; }

    .foot { margin-top: 16px; border-top: 1px solid #e3e0d8; padding-top: 7px; font-size: 8.5px; color: #8b94a3; }
</style>
