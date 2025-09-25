<?php

declare(strict_types=1);

namespace Modules\Permission\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class PermissionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $permissionId = $this->route('permission') ?? $this->route('id');

        return array_merge([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_\.]+$/',
                Rule::unique('permissions', 'name')->ignore($permissionId),
            ],
            'display_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'guard_name' => [
                'required',
                'string',
                'max:255',
                'in:web,api,admin',
            ],
            'is_active' => [
                'boolean',
            ],
            'is_system' => [
                'boolean',
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
                'max:9999',
            ],
            'category' => [
                'nullable',
                'string',
                'max:100',
            ],
            'module' => [
                'nullable',
                'string',
                'max:100',
            ],
            'action' => [
                'nullable',
                'string',
                'max:100',
            ],
            'resource' => [
                'nullable',
                'string',
                'max:100',
            ],
            'level' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],
            'color' => [
                'nullable',
                'string',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'icon' => [
                'nullable',
                'string',
                'max:100',
            ],
            'roles' => [
                'nullable',
                'array',
            ],
            'roles.*' => [
                'integer',
                'exists:roles,id',
            ],
            'users' => [
                'nullable',
                'array',
            ],
            'users.*' => [
                'integer',
                'exists:users,id',
            ],
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'name.required' => 'Permission name is required.',
            'name.regex' => 'Permission name can only contain letters, numbers, spaces, hyphens, underscores, and dots.',
            'name.unique' => 'This permission name is already in use.',
            'name.min' => 'Permission name must be at least 2 characters long.',
            'name.max' => 'Permission name must not exceed 255 characters.',
            'display_name.max' => 'Display name must not exceed 255 characters.',
            'description.max' => 'Description must not exceed 1000 characters.',
            'guard_name.required' => 'Guard name is required.',
            'guard_name.in' => 'Guard name must be web, api, or admin.',
            'guard_name.max' => 'Guard name must not exceed 255 characters.',
            'sort_order.min' => 'Sort order must be at least 0.',
            'sort_order.max' => 'Sort order cannot exceed 9999.',
            'category.max' => 'Category must not exceed 100 characters.',
            'module.max' => 'Module must not exceed 100 characters.',
            'action.max' => 'Action must not exceed 100 characters.',
            'resource.max' => 'Resource must not exceed 100 characters.',
            'level.min' => 'Level must be at least 0.',
            'level.max' => 'Level cannot exceed 100.',
            'color.regex' => 'Color must be a valid hex color code (e.g., #FF0000).',
            'icon.max' => 'Icon must not exceed 100 characters.',
            'roles.max' => 'Maximum 1000 roles are allowed.',
            'roles.*.exists' => 'One or more selected roles do not exist.',
            'users.max' => 'Maximum 10000 users are allowed.',
            'users.*.exists' => 'One or more selected users do not exist.',
        ]);
    }

    /**
     * Additional validation rules.
     */
    protected function additionalValidation($validator): void
    {
        $validator->after(function ($validator): void {
            // Validate name length
            if ($this->filled('name')) {
                $name = $this->name;

                if (mb_strlen($name) < 2) {
                    $validator->errors()->add(
                        'name',
                        'Permission name must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($name) > 255) {
                    $validator->errors()->add(
                        'name',
                        'Permission name must not exceed 255 characters.'
                    );
                }
            }

            // Validate display name
            if ($this->filled('display_name') && mb_strlen($this->display_name) > 255) {
                $validator->errors()->add(
                    'display_name',
                    'Display name must not exceed 255 characters.'
                );
            }

            // Validate description
            if ($this->filled('description') && mb_strlen($this->description) > 1000) {
                $validator->errors()->add(
                    'description',
                    'Description must not exceed 1000 characters.'
                );
            }

            // Validate guard name
            if ($this->filled('guard_name')) {
                $guardName = $this->guard_name;
                $validGuards = ['web', 'api', 'admin'];

                if (! in_array($guardName, $validGuards)) {
                    $validator->errors()->add(
                        'guard_name',
                        'Invalid guard name.'
                    );
                }
            }

            // Validate sort order
            if ($this->filled('sort_order')) {
                $sortOrder = $this->sort_order;

                if ($sortOrder < 0 || $sortOrder > 9999) {
                    $validator->errors()->add(
                        'sort_order',
                        'Sort order must be between 0 and 9999.'
                    );
                }
            }

            // Validate category
            if ($this->filled('category') && mb_strlen($this->category) > 100) {
                $validator->errors()->add(
                    'category',
                    'Category must not exceed 100 characters.'
                );
            }

            // Validate module
            if ($this->filled('module') && mb_strlen($this->module) > 100) {
                $validator->errors()->add(
                    'module',
                    'Module must not exceed 100 characters.'
                );
            }

            // Validate action
            if ($this->filled('action') && mb_strlen($this->action) > 100) {
                $validator->errors()->add(
                    'action',
                    'Action must not exceed 100 characters.'
                );
            }

            // Validate resource
            if ($this->filled('resource') && mb_strlen($this->resource) > 100) {
                $validator->errors()->add(
                    'resource',
                    'Resource must not exceed 100 characters.'
                );
            }

            // Validate level
            if ($this->filled('level')) {
                $level = $this->level;

                if ($level < 0 || $level > 100) {
                    $validator->errors()->add(
                        'level',
                        'Level must be between 0 and 100.'
                    );
                }
            }

            // Validate color format
            if ($this->filled('color')) {
                $color = $this->color;

                if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                    $validator->errors()->add(
                        'color',
                        'Color must be a valid hex color code (e.g., #FF0000).'
                    );
                }
            }

            // Validate icon
            if ($this->filled('icon') && mb_strlen($this->icon) > 100) {
                $validator->errors()->add(
                    'icon',
                    'Icon must not exceed 100 characters.'
                );
            }

            // Validate roles
            if ($this->filled('roles')) {
                $roles = $this->roles;

                if (count($roles) > 1000) {
                    $validator->errors()->add(
                        'roles',
                        'Maximum 1000 roles are allowed.'
                    );
                }

                foreach ($roles as $index => $roleId) {
                    $role = \Spatie\Permission\Models\Role::find($roleId);

                    if (! $role) {
                        $validator->errors()->add(
                            'roles.'.$index,
                            'Role does not exist.'
                        );
                    } elseif (! $role->is_active) {
                        $validator->errors()->add(
                            'roles.'.$index,
                            'Role is not active.'
                        );
                    } elseif ($role->guard_name !== $this->guard_name) {
                        $validator->errors()->add(
                            'roles.'.$index,
                            'Role guard name does not match permission guard name.'
                        );
                    }
                }
            }

            // Validate users
            if ($this->filled('users')) {
                $users = $this->users;

                if (count($users) > 10000) {
                    $validator->errors()->add(
                        'users',
                        'Maximum 10000 users are allowed.'
                    );
                }

                foreach ($users as $index => $userId) {
                    $user = \Modules\User\Models\User::find($userId);

                    if (! $user) {
                        $validator->errors()->add(
                            'users.'.$index,
                            'User does not exist.'
                        );
                    } elseif (! $user->is_active) {
                        $validator->errors()->add(
                            'users.'.$index,
                            'User is not active.'
                        );
                    }
                }
            }

            // Validate system permission restrictions
            if ($this->filled('is_system') && $this->is_system && $this->filled('name')) {
                $name = $this->name;
                $systemPermissions = [
                    'create', 'read', 'update', 'delete', 'view', 'edit',
                    'manage', 'admin', 'super-admin', 'user', 'guest',
                    'moderator', 'editor', 'author', 'contributor',
                ];
                if (! in_array($name, $systemPermissions)) {
                    $validator->errors()->add(
                        'name',
                        'System permission name must be one of: '.implode(', ', $systemPermissions)
                    );
                }
            }

            // Validate permission name uniqueness (case insensitive)
            if ($this->filled('name')) {
                $name = $this->name;
                $permissionId = $this->route('permission') ?? $this->route('id');

                $existingPermission = \Spatie\Permission\Models\Permission::where('name', 'LIKE', $name)
                    ->where('guard_name', $this->guard_name)
                    ->where('id', '!=', $permissionId)
                    ->first();

                if ($existingPermission) {
                    $validator->errors()->add(
                        'name',
                        'A permission with this name already exists for this guard.'
                    );
                }
            }

            // Validate permission name format
            if ($this->filled('name')) {
                $name = $this->name;

                // Check for common permission patterns
                $validPatterns = [
                    '/^[a-z]+$/',
                    '/^[a-z]+\.[a-z]+$/',
                    '/^[a-z]+\.[a-z]+\.[a-z]+$/',
                    '/^[a-z]+-[a-z]+$/',
                    '/^[a-z]+_[a-z]+$/',
                ];

                $isValid = false;
                foreach ($validPatterns as $pattern) {
                    if (preg_match($pattern, $name)) {
                        $isValid = true;

                        break;
                    }
                }

                if (! $isValid) {
                    $validator->errors()->add(
                        'name',
                        'Permission name must follow a valid pattern (e.g., "create", "user.create", "admin.user.create").'
                    );
                }
            }
        });
    }
}
