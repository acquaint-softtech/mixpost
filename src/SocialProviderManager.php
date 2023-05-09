<?php

namespace Inovector\Mixpost;

use Inovector\Mixpost\Abstracts\SocialProviderManager as SocialProviderManagerAbstract;
use Inovector\Mixpost\Facades\Services;
use Inovector\Mixpost\SocialProviders\FacebookGroupProvider;
use Inovector\Mixpost\SocialProviders\FacebookPageProvider;
use Inovector\Mixpost\SocialProviders\TwitterProvider;
use Inovector\Mixpost\SocialProviders\MastodonProvider;

class SocialProviderManager extends SocialProviderManagerAbstract
{
    protected function connectTwitterProvider()
    {
        $config = Services::get_data('twitter');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'twitter']);

        return $this->buildConnectionProvider(TwitterProvider::class, $config);
    }

    protected function connectFacebookPageProvider()
    {
        $config = Services::get_data('facebook');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'facebook_page']);

        return $this->buildConnectionProvider(FacebookPageProvider::class, $config);
    }

    protected function connectFacebookGroupProvider()
    {
        $config = Services::get_data('facebook');

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'facebook_group']);

        return $this->buildConnectionProvider(FacebookGroupProvider::class, $config);
    }

    protected function connectMastodonProvider()
    {
        $request = $this->container->request;
        $sessionServerKey = "{$this->config->get('mixpost.cache_prefix')}.mastodon_server";

        if ($request->route() && $request->route()->getName() === 'mixpost.accounts.add') {
            $serverName = $this->container->request->input('server');
            $request->session()->put($sessionServerKey, $serverName); // We keep the server name in the session. We'll need it in the callback
        } else if ($request->route() && $request->route()->getName() === 'mixpost.callbackSocialProvider') {
            $serverName = $request->session()->get($sessionServerKey);
        } else {
            $serverName = $this->values['data']['server']; // Get the server value that have been set on SocialProviderManager::connect($provider, array $values = [])
        }

        $config = Services::get_data("mastodon.$serverName");

        $config['redirect'] = route('mixpost.callbackSocialProvider', ['provider' => 'mastodon']);
        $config['values'] = [
            'data' => ['server' => $serverName]
        ];

        return $this->buildConnectionProvider(MastodonProvider::class, $config);
    }
}
