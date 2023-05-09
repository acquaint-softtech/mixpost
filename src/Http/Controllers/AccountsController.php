<?php

namespace Inovector\Mixpost\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Inovector\Mixpost\Actions\UpdateOrCreateAccount;
use Inovector\Mixpost\Facades\Services;
use Inovector\Mixpost\Facades\SocialProviderManager;
use Inovector\Mixpost\Http\Resources\AccountResource;
use Inovector\Mixpost\Models\Account;
use Inovector\Mixpost\Support\Log;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AccountsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Accounts/Accounts', [
            'accounts' => AccountResource::collection(Account::latest()->get())->resolve(),
            'has_service' => [
                'twitter' => !!Services::query()->get('twitter', 'client_id'),
                'facebook' => !!Services::query()->get('facebook', 'client_id')
            ]
        ]);
    }

    public function update(Account $account): RedirectResponse
    {
        $provider = SocialProviderManager::connect($account->provider, $account->values());
        $provider->setAccessToken($account->access_token->toArray());

        $result = $provider->getAccount();

        if (isset($result['error'])) {
            if ($result['error']['status'] === HttpResponse::HTTP_UNAUTHORIZED) {
                return redirect()->back()->with('error', 'The account cannot be updated. Re-authenticate your account.');
            }

            Log::error("Update Account: {$result['error']['desc']}", $account->toArray());

            return redirect()->back()->with('error', 'The account cannot be updated. Internal Error!.');
        }

        (new UpdateOrCreateAccount())($account->provider, $result, $account->access_token->toArray());

        return redirect()->back();
    }

    public function delete(Account $account): RedirectResponse
    {
        $account->delete();

        return redirect()->back();
    }
}
