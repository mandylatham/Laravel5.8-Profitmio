<?php
namespace App\Observers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Model;

use Lab404\Impersonate\Services\ImpersonateManager;

use App\Models\User;
use App\Models\Impersonation\ImpersonatedAction;

class ImpersonateObserver
{
    /**
     * @var ImpersonateManager
     */
    protected $manager;
    /**
     * @var User
     */
    protected $user;
    public function __construct(ImpersonateManager $manager, Guard $auth)
    {
        $this->manager = $manager;
        $this->user = $auth->user();
    }
    /**
     * Generic function for all types of events
     *
     * @param Model $model
     * @param int $type
     */
    protected function logAction(Model $model, int $type): void
    {
        if (!$this->manager->isImpersonating()) {
            return;
        }
        $action = new ImpersonatedAction([
            'user_id' => $this->user->getAuthIdentifier(),
            'impersonator_id' => $this->manager->getImpersonatorId(),
            'action' => $type,
        ]);
        $action->object()->associate($model);
        $action->save();
    }
    /**
     * Handle the "created" event.
     *
     * @param Model $model
     * @return void
     */
    public function created(Model $model): void
    {
        $this->logAction($model, ImpersonatedAction::TYPE_CREATE);
    }
}
