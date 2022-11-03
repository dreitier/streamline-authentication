<?php

namespace Dreitier\Streamline\Authentication\Methods\FormBased\Requests;

use Dreitier\Streamline\Authentication\Package;
use Dreitier\Streamline\Authentication\Repositories\Contracts\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class FormBasedRequest extends \Illuminate\Foundation\Http\FormRequest
{
    private $resolvedUser;

    /**
     * Overwrite the error bag to 'form'
     *
     * @var string
     */
    protected $errorBag = 'form';

    public function __construct(public readonly UserRepository $userRepository)
    {
    }

    public function authorized()
    {
        return true;
    }

    public function getResolvedUser(): mixed
    {
        return $this->resolvedUser;
    }

    public function rules()
    {
        $rules = [
            Package::config('methods.form_based.form.principal.name') => ['bail', 'required',
                function ($attribute, $value, $fail) {
                    try {
                        $this->checkTooManyFailedAttempts();
                    } catch (\Exception $e) {
                        return $fail($e->getMessage());
                    }
                },
                function ($attribute, $value, $fail) {
                    /*
                        try {
                            if (!$this->isMailAddressInDomain($this->getCurrentTenant(), $value)) {
                                return $fail(__("Mail does not belong to allowed domains"));
                            }
                        } catch (\Exception $e) {
                            return $fail($e->getMessage());
                        }
                        */
                }, function ($attribute, $value, $fail) {
                    $this->resolvedUser = $this->userRepository->find(Package::configWithDefault('methods.form_based.find_by_key', 'email'), $value);

                    if (! $this->resolvedUser) {
                        return $fail('Authentication failed (400');
                    }
                }, ],
            Package::config('methods.form_based.form.password.name') => ['required', function ($attribute, $value, $fail) {
                if (! $this->resolvedUser) {
                    return;
                }

                if (! Hash::check($value, $this->resolvedUser->password)) {
                    return $fail('Authentication failed (401)');
                }
            }],
        ];

        return $rules;
    }

    /**
     * Configure additional validation behaviour
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(\Illuminate\Validation\Validator $validator)
    {
        // after the validation has ended with success or failure, we need to update the rate limiter
        $validator->after(function () use ($validator) {
            if ($validator->failed()) {
                RateLimiter::hit($this->throttleKey(), $seconds = 3600);
            } else {
                RateLimiter::clear($this->throttleKey());
            }
        });
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     */
    public function checkTooManyFailedAttempts()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 10)) {
            return;
        }

        throw new \Exception('IP address banned. Too many login attempts.');
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower(request(Package::config('methods.form_based.form.principal.name'))).'|'.request()->ip();
    }
}
