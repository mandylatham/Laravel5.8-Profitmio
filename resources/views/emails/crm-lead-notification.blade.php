<?adf version="1.0"?>
<?xml version="1.0"?>
<adf>
    <prospect>
        <requestdate>{{ date(DATE_ATOM, time()) }}</requestdate>
        <vehicle>
            <year>{{ $recipient->year }}</year>
            <make>{{ $recipient->make }}</make>
            <model>{{ $recipient->model }}</model>
        </vehicle>
        <customer>
            <contact>
                <name part="first">{{ $recipient->first_name }}</name>
                <name part="last">{{ $recipient->last_name }}</name>
                <email>{{ $recipient->email }}</email>
                <phone type="voice">{{ $recipient->phone }}</phone>
                <address type="home">
                    <street line="1">{{ $recipient->address }}</street>
                    <apartment/>
                    <city>{{ $recipient->city }}</city>
                    <regioncode>{{ $recipient->state }}</regioncode>
                    <postalcode>{{ $recipient->zip }}</postalcode>
                </address>
            </contact>
            <comments>This lead, {{ $recipient->first_name }} {{ $recipient->last_name }} has been manually submitted to your CRM by Profit Miner user, {{ $user->name }}</comments>
        </customer>
        <vendor>
            <vendorname>{{ $campaign->agency->name }}</vendorname>
            <contact primarycontact="1">
                <name part="full">{{ $campaign->agency->name }}</name>
            </contact>
        </vendor>
        <provider>
            <id source="Profit Miner"></id>
            <name>Profit Miner</name>
        </provider>
    </prospect>
</adf>
