function confirmMsg( msg )
{
	var msg = msg || "Czy na pewno?";
    var agree = confirm( msg );
    if( agree )
        return true;
    else
        return false;
}

