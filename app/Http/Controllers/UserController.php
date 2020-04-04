<?php

namespace App\Http\Controllers;

use App\Classes\CompanyUserActivityLog;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateCompanyDataRequest;
use App\Http\Resources\UserCollection;
use App\Models\Company;
use App\Mail\InviteUser;
use App\Models\User;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Storage;

class UserController extends Controller
{
    private $company;
    /** @var CompanyUserActivityLog  */
    private $companyUserActivityLog;
    private $storage;
    private $hasher;
    private $user;

    public function __construct(Company $company, CompanyUserActivityLog $companyUserActivityLog, FilesystemManager $storage, BcryptHasher $hasher, User $user)
    {
        $this->company = $company;
        $this->companyUserActivityLog = $companyUserActivityLog;
        $this->storage = $storage;
        $this->hasher = $hasher;
        $this->user = $user;
    }

    public function activate(User $user, Request $request)
    {
        $user->activate($request->input('company'));
        return response()->json(['message' => 'User activated.']);
    }

    /**
     * Return all companies for user display
     * @param Request $request
     * @return mixed
     */
    public function getForUserDisplay(Request $request)
    {
        $userQuery = $this->user->searchByRequest($request);
        $users = $userQuery
            ->select('users.*')
            ->orderBy('users.id', 'desc')
            ->paginate(15);

        return new UserCollection($users);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('user.index', []);
    }

    public function create(Request $request)
    {
        $data = [];
        if ($request->has('company') && auth()->user()->isAdmin()) {
            $data['company'] = Company::findOrFail($request->input('company'));
        } else {
            $data = [
                'companies' => $this->company->all()
            ];
        }
        return view('user.create', $data);
    }

    public function deactivate(User $user, Request $request)
    {
        $user->deactivate($request->input('company'));
        return response()->json(['message' => 'User deactivated.']);
    }

    public function delete(User $user, Request $request)
    {
        $user->delete();
    }

    /**
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(StoreUserRequest $request)
    {
	    \Log::debug(json_encode($request->all()));
        $user = $this->user
            ->where('email', $request->input('email'))
            ->first();

        if ($request->input('role') == 'site_admin' && $user && $user->isAdmin()) {
            return response()->json(['error' => 'The email has already been taken.'], 400);
        }
        if (!$user) {
            $user = new $this->user();
            $user->is_admin = $request->input('role') == 'site_admin' ? true : false;
            $user->password = '';
            $user->first_name = '';
            $user->last_name = '';
            $user->email = $request->input('email');
            $user->save();
        }

        $urlData = [];
        if ($user->isAdmin()) {
            $company = $this->company->where('type', 'support')->first();
        } else {
            // Attach to company if user is not admin
            if (auth()->user()->isAdmin()) {
                $company = $this->company->findOrFail($request->input('company'));
            } else {
                $company = $this->company->findOrFail(get_active_company());
            }
        }

        $urlData = [
            'id' => $user->getKey(),
            'company' => $company->id
        ];

        $this->companyUserActivityLog->attach($user, $company->id, $request->input('role'));

        $user->companies()->attach($company->id, [
            'role' => $request->input('role', 'user')
        ]);

        $processRegistration = URL::temporarySignedRoute(
            'registration.complete.show', Carbon::now()->addHours(48), $urlData
        );

        Mail::to($user)->send(new InviteUser($user, $company, $processRegistration));

        return response()->json([], 201);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createForm()
    {
        $viewData = [
            'companies' => Company::all()
        ];
        return view('user.new', $viewData);
    }

    /**
     * Return the view to select a company
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function selectActiveCompany(Request $request)
    {
        return view('user.select-company');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user = null)
    {
        if (! $user) $user = auth()->user();
        $viewData['user'] = $user;
        $viewData['campaigns'] = collect([]);

        if ($user->isAgencyUser()) {
            $viewData['campaigns'] = $user->agencyCampaigns()->get();
        }
        if ($user->access == 'Client') {
            $viewData['campaigns'] = $user->campaigns()->get();
        }

        $viewData['campaignCompanySelected'] = null;
        $viewData['campaignQ'] = '';
        $viewData['companyQ'] = '';

        return view('user.details', $viewData);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('user.edit', [
            'user' => $user,
            'companies' => $user->companies()->orderBy('name', 'asc')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->except(['password', 'email']));

        return response()->json([]);

//        return response()->redirectToRoute('user.edit', ['user' => $user->id]);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, User $user)
    {
        $validator = $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (! $this->hasher->check($request->input('old_password'), $user->password)) {
            $errors = new MessageBag(['errors' => ['old_password' => ['Invalid current password']]]);
            return response()->json($errors, 422);
        }

        $user->update(['password' => bcrypt($request->input('password'))]);;

        return response()->json([], 200);
    }

    public function updateForm(User $user)
    {
        $viewData['user'] = $user;
        $viewData['companies'] = Company::all();

        return view('user.edit', $viewData);
    }

    public function updateCompanyData(User $user, UpdateCompanyDataRequest $request)
    {
        $invitation = $user->invitations()->where('company_id', $request->input('company'))->firstOrFail();

        if ($request->has('timezone')) {
            $config = $invitation->config;
            $config['timezone'] = $request->input('timezone');
            $invitation->config = $config;
        }
        if ($request->has('role') && $request->input('role') !== 'site_admin') {
            $invitation->role = $request->input('role');
        }
        $invitation->save();

        return response()->json('Resource updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function updateAvatar(User $user, Request $request, FileReceiver $receiver)
    {
        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }
        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            $image = $user->sudoaddMedia($save->getFile())->toMediaCollection('profile-photo', env('MEDIA_LIBRARY_DEFAULT_PUBLIC_FILESYSTEM'));
            return response()->json([
                'location' => Storage::disk($image->disk)->url($image->id.'/'.$image->file_name)
            ], 201);
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();
        return response()->json([
            "done" => $handler->getPercentageDone()
        ]);
    }

    public function view(User $user = null)
    {
        $loggedUser = auth()->user();
        if (! $user) {
            $user = $loggedUser;
        }
        if ($loggedUser->isAdmin()) {
            $hasCampaigns = $user->getCampaigns()->count() > 0;
        } else {
            $company = Company::findOrFail(get_active_company());
            if ($company->isDealership()) {
                $field = 'dealership_id';
            } else {
                $field = 'agency_id';
            }
            $hasCampaigns = $user->getCampaigns()->where($field, get_active_company())->count() > 0;
        }
        return view('user.detail', [
            'user' => $user,
            'isProfile' => $user->id === auth()->user()->id,
            'hasCampaigns' => $hasCampaigns,
            'timezones' => $user->getPossibleTimezonesForUser(),
            'userRole' => auth()->user()->getRole(Company::findOrFail(get_active_company())),
        ]);
    }
}
