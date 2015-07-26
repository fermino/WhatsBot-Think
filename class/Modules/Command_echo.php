<?php
	$Text = $Message->GetText($ModuleName);

	if($Text !== false)
		$WhatsApp->SendRawMessage($Message->From, $Text);
	else
		return SEND_USAGE;