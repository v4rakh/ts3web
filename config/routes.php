<?php
/*
 * Actions & Routes
 * CORE
 */
$container[NotFoundAction::class] = function ($container) {
    return new NotFoundAction($container);
};
$app->get('/404', NotFoundAction::class)->setName('404');

$container[NotAuthorizedAction::class] = function ($container) {
    return new NotAuthorizedAction($container);
};
$app->get('/401', NotAuthorizedAction::class)->setName('401');

$container[ForbiddenAction::class] = function ($container) {
    return new ForbiddenAction($container);
};
$app->get('/403', ForbiddenAction::class)->setName('403');

$container[InternalApplicationError::class] = function ($container) {
    return new InternalApplicationError($container);
};
$app->get('/500', InternalApplicationError::class)->setName('500');

$container[IndexAction::class] = function ($container) {
    return new IndexAction($container);
};
$app->get('/', IndexAction::class);

$container[AuthLoginAction::class] = function ($container) {
    return new AuthLoginAction($container);
};
$app->map(['GET', 'POST'], '/login', AuthLoginAction::class);

$container[AuthLogoutAction::class] = function ($container) {
    return new AuthLogoutAction($container);
};
$app->get('/logout', AuthLogoutAction::class);

/*
 * teamspeak
 */
// profile
$container[ProfileAction::class] = function ($container) {
    return new ProfileAction($container);
};
$app->get('/profile', ProfileAction::class);

$container[ProfileCredentialsChangeAction::class] = function ($container) {
    return new ProfileCredentialsChangeAction($container);
};
$app->get('/profile/credentials', ProfileCredentialsChangeAction::class);

// log
$container[LogsAction::class] = function ($container) {
    return new LogsAction($container);
};
$app->get('/logs[/{sid}]', LogsAction::class);

// instance
$container[InstanceAction::class] = function ($container) {
    return new InstanceAction($container);
};
$app->get('/instance', InstanceAction::class);

$container[InstanceEditAction::class] = function ($container) {
    return new InstanceEditAction($container);
};
$app->post('/instance/edit', InstanceEditAction::class);

// server
$container[ServersAction::class] = function ($container) {
    return new ServersAction($container);
};
$app->get('/servers', ServersAction::class);

$container[ServerCreateAction::class] = function ($container) {
    return new ServerCreateAction($container);
};
$app->post('/servers/create', ServerCreateAction::class);

$container[ServerSelectAction::class] = function ($container) {
    return new ServerSelectAction($container);
};
$app->get('/servers/select/{sid}', ServerSelectAction::class);

$container[ServerDeselectAction::class] = function ($container) {
    return new ServerDeselectAction($container);
};
$app->get('/servers/deselect', ServerDeselectAction::class);

$container[ServerDeleteAction::class] = function ($container) {
    return new ServerDeleteAction($container);
};
$app->get('/servers/delete/{sid}', ServerDeleteAction::class);

$container[ServerInfoAction::class] = function ($container) {
    return new ServerInfoAction($container);
};
$app->get('/servers/{sid}', ServerInfoAction::class);

$container[ServerStartAction::class] = function ($container) {
    return new ServerStartAction($container);
};
$app->get('/servers/start/{sid}', ServerStartAction::class);

$container[ServerStopAction::class] = function ($container) {
    return new ServerStopAction($container);
};
$app->get('/servers/stop/{sid}', ServerStopAction::class);

$container[ServerSendAction::class] = function ($container) {
    return new ServerSendAction($container);
};
$app->post('/servers/send/{sid}', ServerSendAction::class);

$container[ServerEditAction::class] = function ($container) {
    return new ServerEditAction($container);
};
$app->post('/servers/edit/{sid}', ServerEditAction::class);

// clients
$container[ClientsAction::class] = function ($container) {
    return new ClientsAction($container);
};
$app->get('/clients/{sid}', ClientsAction::class);

$container[ClientInfoAction::class] = function ($container) {
    return new ClientInfoAction($container);
};
$app->get('/clients/{sid}/{cldbid}', ClientInfoAction::class);

$container[ClientDeleteAction::class] = function ($container) {
    return new ClientDeleteAction($container);
};
$app->get('/clients/delete/{sid}/{cldbid}', ClientDeleteAction::class);

$container[ClientBanAction::class] = function ($container) {
    return new ClientBanAction($container);
};
$app->post('/clients/ban/{sid}/{cldbid}', ClientBanAction::class);

$container[ClientSendAction::class] = function ($container) {
    return new ClientSendAction($container);
};
$app->post('/clients/send/{sid}/{cldbid}', ClientSendAction::class);

// online
$container[OnlineAction::class] = function ($container) {
    return new OnlineAction($container);
};
$app->get('/online/{sid}', OnlineAction::class);

$container[OnlineInfoAction::class] = function ($container) {
    return new OnlineInfoAction($container);
};
$app->get('/online/{sid}/{clid}', OnlineInfoAction::class);

$container[OnlinePokeAction::class] = function ($container) {
    return new OnlinePokeAction($container);
};
$app->post('/online/poke/{sid}/{clid}', OnlinePokeAction::class);

$container[OnlineKickAction::class] = function ($container) {
    return new OnlineKickAction($container);
};
$app->post('/online/kick/{sid}/{clid}', OnlineKickAction::class);

$container[OnlineBanAction::class] = function ($container) {
    return new OnlineBanAction($container);
};
$app->post('/online/ban/{sid}/{clid}', OnlineBanAction::class);

$container[OnlineSendAction::class] = function ($container) {
    return new OnlineSendAction($container);
};

$app->post('/online/send/{sid}/{clid}', OnlineSendAction::class);

// group
$container[GroupsAction::class] = function ($container) {
    return new GroupsAction($container);
};
$app->get('/groups/{sid}', GroupsAction::class);

// server group
$container[ServerGroupInfoAction::class] = function ($container) {
    return new ServerGroupInfoAction($container);
};
$app->get('/servergroups/{sid}/{sgid}', ServerGroupInfoAction::class);

$container[ServerGroupDeleteAction::class] = function ($container) {
    return new ServerGroupDeleteAction($container);
};
$app->get('/servergroups/delete/{sid}/{sgid}', ServerGroupDeleteAction::class);

$container[ServerGroupRemoveAction::class] = function ($container) {
    return new ServerGroupRemoveAction($container);
};
$app->get('/servergroups/remove/{sid}/{sgid}/{cldbid}', ServerGroupRemoveAction::class);

$container[ServerGroupAddAction::class] = function ($container) {
    return new ServerGroupAddAction($container);
};
$app->post('/servergroups/add/{sid}/{sgid}', ServerGroupAddAction::class);

$container[ServerGroupRenameAction::class] = function ($container) {
    return new ServerGroupRenameAction($container);
};
$app->post('/servergroups/rename/{sid}/{sgid}', ServerGroupRenameAction::class);

$container[ServerGroupCreateAction::class] = function ($container) {
    return new ServerGroupCreateAction($container);
};
$app->post('/servergroups/create/{sid}', ServerGroupCreateAction::class);


// channel group
$container[ChannelGroupInfoAction::class] = function ($container) {
    return new ChannelGroupInfoAction($container);
};
$app->get('/channelgroups/{sid}/{cgid}', ChannelGroupInfoAction::class);

$container[ChannelGroupDeleteAction::class] = function ($container) {
    return new ChannelGroupDeleteAction($container);
};
$app->get('/channelgroups/delete/{sid}/{cgid}', ChannelGroupDeleteAction::class);

$container[ChannelGroupRenameAction::class] = function ($container) {
    return new ChannelGroupRenameAction($container);
};
$app->post('/channelgroups/rename/{sid}/{cgid}', ChannelGroupRenameAction::class);

$container[ChannelGroupCreateAction::class] = function ($container) {
    return new ChannelGroupCreateAction($container);
};
$app->post('/channelgroups/create/{sid}', ChannelGroupCreateAction::class);

// ban
$container[BansAction::class] = function ($container) {
    return new BansAction($container);
};
$app->get('/bans/{sid}', BansAction::class);

$container[BanDeleteAction::class] = function ($container) {
    return new BanDeleteAction($container);
};
$app->get('/bans/delete/{sid}/{banId}', BanDeleteAction::class);

// complain
$container[ComplainsAction::class] = function ($container) {
    return new ComplainsAction($container);
};
$app->get('/complains/{sid}', ComplainsAction::class);

$container[ComplainDeleteAction::class] = function ($container) {
    return new ComplainDeleteAction($container);
};
$app->get('/complains/delete/{sid}/{tcldbid}', ComplainDeleteAction::class);

// channel
$container[ChannelsAction::class] = function ($container) {
    return new ChannelsAction($container);
};
$app->get('/channels/{sid}', ChannelsAction::class);

$container[ChannelInfoAction::class] = function ($container) {
    return new ChannelInfoAction($container);
};
$app->get('/channels/{sid}/{cid}', ChannelInfoAction::class);

$container[ChannelEditAction::class] = function ($container) {
    return new ChannelEditAction($container);
};
$app->post('/channels/edit/{sid}/{cid}', ChannelEditAction::class);

$container[ChannelDeleteAction::class] = function ($container) {
    return new ChannelDeleteAction($container);
};
$app->get('/channels/delete/{sid}/{cid}', ChannelDeleteAction::class);

$container[ChannelSendAction::class] = function ($container) {
    return new ChannelSendAction($container);
};
$app->post('/channels/send/{sid}/{cid}', ChannelSendAction::class);

// tokens
$container[TokensAction::class] = function ($container) {
    return new TokensAction($container);
};
$app->get('/tokens/{sid}', TokensAction::class);

$container[TokenAddAction::class] = function ($container) {
    return new TokenAddAction($container);
};
$app->post('/tokens/add/{sid}', TokenAddAction::class);

$container[TokenDeleteAction::class] = function ($container) {
    return new TokenDeleteAction($container);
};
$app->get('/tokens/delete/{sid}/{token}', TokenDeleteAction::class);
