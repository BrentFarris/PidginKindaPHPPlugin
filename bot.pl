use Purple;
use Pidgin;

use HTML::Strip;
use LWP::Simple;

%PLUGIN_INFO = (
	perl_api_version => 2,
	name => "Brents Plugin",
	version => "1.0",
	summary => "A plugin that allows the reading and responding of messages$
	description => "This plugin allows you to write responses and consume t$
	author => "Brent A. Farris <brent\@beardedmangames.com",
	url => "https://www.beardedmangames.com",
	load => "plugin_load",
	unload => "plugin_unload"
);

sub starts_with_cmd {
	my $hs = HTML::Strip->new();
	my $message = $hs->parse(shift . " ");
	my $cmd = shift;
	$hs->eof;

	Purple::Debug::info("BRENTS PLUGIN", $message . "\n");

	return $message =~ /^(\/$cmd\s)/;
}

sub check {
	my $hs = HTML::Strip->new();
	my $message = $hs->parse(shift);
	my $conv = shift;
	my $sender = shift;
	my $work = get("http://127.0.0.1/pidgin/index.php?sender=" . $sender . $

	if ($work ne "") {
		$conv->get_im_data()->send($work);
	}

	#if (starts_with_cmd($message, "roll")) {
	#	#$conv->get_im_data()->write("SENDER", "Rolling", 0, 0);
	#	$conv->get_im_data()->send("Rolling");
	#}

	#if (starts_with_cmd($message, "google")) {
	#	$conv->get_im_data()->send($loc);
	#}
}

sub sent_im_msg {
	my ($account, $receiver, $message) = @_;
	my $conv = Purple::Conversation->new(1, $account, $receiver);
	
	#Purple::Debug::info("BRENTS PLUGIN", $message . "\n");

	check($message, $conv, $account->get_username());
}

sub received_im_msg {
	my ($account, $sender, $message, $conv, $flags) = @_;
	check($message, $conv, $sender);
}

sub sent_chat_msg {
	my ($account, $message, $id) = @_;
	my $conv = Purple::Conversation::Chat::purple_find_chat($account->get_connection(), $id);
	check($message, $conv, $account->get_username());
}

sub received_chat_msg {
	my ($account, $sender, $message, $conv, $flags) = @_;
	check($message, $conv, $sender);
}

sub plugin_init {
	return %PLUGIN_INFO;
}

sub plugin_unload {
	my $plugin = shift;
	Purple::Debug::info("BRENTS PLUGIN", "plugin_unload() - Test Plugin Unloaded.\n");
}

sub plugin_load {
	my $plugin = shift;
	my $protocol = "prpl-aim";
	my $account_name = "baflink";
	$account = Purple::Accounts::find($account_name, $protocol);

	# Accounts
	#$act_handle = Purple::Accounts::get_handle();
	#Purple::Signal::connect($act_handle, "account-connecting", $plugin, \&account_connecting_cb, 0);

	# Buddy List
	#$blist = Purple::BuddyList::get_handle();
	#Purple::Signal::connect($blist, "buddy-signed-on", $plugin, \&buddy_signed_on, 0);

	# Connections
	#$conn = Purple::Connections::get_handle();
	#Purple::Signal::connect($conn, "signed-on", $plugin, \&signed_on, 0);

	# Conversations
	$conv = Purple::Conversations::get_handle();
	Purple::Signal::connect($conv, "received-im-msg", $plugin, \&received_im_msg, "received im message");
	Purple::Signal::connect($conv, "received-chat-msg", $plugin, \&received_chat_msg, "received chat message");
	Purple::Signal::connect($conv, "sent-im-msg", $plugin, \&sent_im_msg, "received im message");
	Purple::Signal::connect($conv, "sent-chat-msg", $plugin, \&sent_chat_msg, "received chat message");

	# Here we send messages to the conversation
	#print "Testing Purple::Conversation::IM::send()...\n";
	#$im->send("Message Test.");
	#print "Testing Purple::Conversation::IM::write()...\n";
	#$conv2->get_im_data()->write("SENDER", "<b>Message</b> Test.", 0, 0);

	#Purple::Debug::info("BRENTS PLUGIN", "Debugging stuff.\n");
}
