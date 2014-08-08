use Purple;
use Pidgin;

use HTML::Strip;
use LWP::Simple;

%PLUGIN_INFO = (
	perl_api_version => 2,
	name => "Brents Plugin",
	version => "1.02",
	summary => "A plugin that allows the reading and responding of messages in PHP via GET.",
	description => "This plugin allows you to write responses and consume text from ims and chats via PHP GET requests.",
	author => "Brent A. Farris <brent\@beardedmangames.com",
	url => "https://www.beardedmangames.com",
	load => "plugin_load",
	unload => "plugin_unload"
);

sub check {
	my $hs = HTML::Strip->new();
	my $message = $hs->parse(shift);
	my $conv = shift;
	my $me = shift;
	my $sender = shift;
	my $work = get("http://127.0.0.1/pidgin/index.php?me=".$me."&sender=".$sender."&msg=".$message);

	#Purple::Debug::info("BRENTS PLUGIN", "me ".$me."\n");
	#Purple::Debug::info("BRENTS PLUGIN", "sender ".$sender."\n");
	#Purple::Debug::info("BRENTS PLUGIN", "message ".$message."\n");
	#Purple::Debug::info("BRENTS PLUGIN", "work ".$work."\n");

	if ($work ne "" && $work !~ /^([wW]arning:)/ && $work !~ /^([fF]atal [eE]rror:)/ && $work !~ /^([pP]arse [eE]rror:)/ && $work !~ /^([nN]otice:)/) {
		if (defined $conv->get_im_data()) {
			$conv->get_im_data()->send($work);
		} elsif (defined $conv->get_chat_data()) {
			$conv->get_chat_data()->send($work);
		}
	}
	
	#Purple::Debug::info("BRENTS PLUGIN", $message . "\n");
}

sub sent_im_msg {
	my ($account, $receiver, $message) = @_;
	my $conv = Purple::Conversation->new(1, $account, $receiver);
	
	check($message, $conv, $account->get_username(), $account->get_username());
}

sub received_im_msg {
	my ($account, $sender, $message, $conv, $flags) = @_;
	if ($account->get_username() ne $sender) {
		check($message, $conv, $account->get_username(), $sender);
	}
}

sub sent_chat_msg {
	my ($account, $message, $id) = @_;
	my $conv = Purple::Conversation::Chat::purple_find_chat($account->get_connection(), $id);
	check($message, $conv, $account->get_username(), $account->get_username());
}

sub received_chat_msg {
	my ($account, $sender, $message, $conv, $flags) = @_;
	if ($account->get_username() ne $sender) {
		check($message, $conv, $account->get_username(), $sender);
	}
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

	# Conversations
	$conv = Purple::Conversations::get_handle();
	Purple::Signal::connect($conv, "received-im-msg", $plugin, \&received_im_msg, "received im message");
	Purple::Signal::connect($conv, "received-chat-msg", $plugin, \&received_chat_msg, "received chat message");
	Purple::Signal::connect($conv, "sent-im-msg", $plugin, \&sent_im_msg, "received im message");
	Purple::Signal::connect($conv, "sent-chat-msg", $plugin, \&sent_chat_msg, "received chat message");
}
