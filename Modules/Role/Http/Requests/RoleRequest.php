<?php

declare(strict_types=1);

namespace Modules\Role\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\BaseRequest;

class RoleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $roleId = $this->route('role') ?? $this->route('id');

        return array_merge([
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
                Rule::unique('roles', 'name')->ignore($roleId),
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
            'permissions' => [
                'nullable',
                'array',
            ],
            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
            'users' => [
                'nullable',
                'array',
            ],
            'users.*' => [
                'integer',
                'exists:users,id',
            ],
            'parent_role_id' => [
                'nullable',
                'integer',
                'exists:roles,id',
                'different:'.$roleId,
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
        ], $this->getCommonRules());
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'name.required' => 'Role name is required.',
            'name.regex' => 'Role name can only contain letters, numbers, spaces, hyphens, and underscores.',
            'name.unique' => 'This role name is already in use.',
            'name.min' => 'Role name must be at least 2 characters long.',
            'name.max' => 'Role name must not exceed 255 characters.',
            'display_name.max' => 'Display name must not exceed 255 characters.',
            'description.max' => 'Description must not exceed 1000 characters.',
            'guard_name.required' => 'Guard name is required.',
            'guard_name.in' => 'Guard name must be web, api, or admin.',
            'guard_name.max' => 'Guard name must not exceed 255 characters.',
            'sort_order.min' => 'Sort order must be at least 0.',
            'sort_order.max' => 'Sort order cannot exceed 9999.',
            'permissions.max' => 'Maximum 1000 permissions are allowed.',
            'permissions.*.exists' => 'One or more selected permissions do not exist.',
            'users.max' => 'Maximum 10000 users are allowed.',
            'users.*.exists' => 'One or more selected users do not exist.',
            'parent_role_id.exists' => 'Parent role does not exist.',
            'parent_role_id.different' => 'Role cannot be its own parent.',
            'level.min' => 'Level must be at least 0.',
            'level.max' => 'Level cannot exceed 100.',
            'color.regex' => 'Color must be a valid hex color code (e.g., #FF0000).',
            'icon.max' => 'Icon must not exceed 100 characters.',
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
                        'Role name must be at least 2 characters long.'
                    );
                }

                if (mb_strlen($name) > 255) {
                    $validator->errors()->add(
                        'name',
                        'Role name must not exceed 255 characters.'
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

            // Validate permissions
            if ($this->filled('permissions')) {
                $permissions = $this->permissions;

                if (count($permissions) > 1000) {
                    $validator->errors()->add(
                        'permissions',
                        'Maximum 1000 permissions are allowed.'
                    );
                }

                foreach ($permissions as $index => $permissionId) {
                    $permission = \Spatie\Permission\Models\Permission::find($permissionId);

                    if (! $permission) {
                        $validator->errors()->add(
                            'permissions.'.$index,
                            'Permission does not exist.'
                        );
                    } elseif ($permission->guard_name !== $this->guard_name) {
                        $validator->errors()->add(
                            'permissions.'.$index,
                            'Permission guard name does not match role guard name.'
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

            // Validate parent role
            if ($this->filled('parent_role_id')) {
                $parentRoleId = $this->parent_role_id;
                $roleId = $this->route('role') ?? $this->route('id');

                if ($parentRoleId === $roleId) {
                    $validator->errors()->add(
                        'parent_role_id',
                        'Role cannot be its own parent.'
                    );
                }

                $parentRole = \Spatie\Permission\Models\Role::find($parentRoleId);
                if (! $parentRole) {
                    $validator->errors()->add(
                        'parent_role_id',
                        'Parent role does not exist.'
                    );
                } elseif (! $parentRole->is_active) {
                    $validator->errors()->add(
                        'parent_role_id',
                        'Parent role is not active.'
                    );
                } elseif ($parentRole->guard_name !== $this->guard_name) {
                    $validator->errors()->add(
                        'parent_role_id',
                        'Parent role guard name does not match role guard name.'
                    );
                }

                // Check for circular reference
                if ($this->isCircularReference($roleId, $parentRoleId)) {
                    $validator->errors()->add(
                        'parent_role_id',
                        'Cannot set parent role as it would create a circular reference.'
                    );
                }
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

            // Validate system role restrictions
            if ($this->filled('is_system') && $this->is_system && $this->filled('name')) {
                $name = $this->name;
                $systemRoles = ['super-admin', 'admin', 'user', 'guest', 'moderator'];
                if (! in_array($name, $systemRoles)) {
                    $validator->errors()->add(
                        'name',
                        'System role name must be one of: '.implode(', ', $systemRoles)
                    );
                }
            }

            // Validate role name uniqueness (case insensitive)
            if ($this->filled('name')) {
                $name = $this->name;
                $roleId = $this->route('role') ?? $this->route('id');

                $existingRole = \Spatie\Permission\Models\Role::where('name', 'LIKE', $name)
                    ->where('guard_name', $this->guard_name)
                    ->where('id', '!=', $roleId)
                    ->first();

                if ($existingRole) {
                    $validator->errors()->add(
                        'name',
                        'A role with this name already exists for this guard.'
                    );
                }
            }
        });
    }

    /**
     * Check for circular reference in role hierarchy.
     */
    private function isCircularReference(int $roleId, int $parentRoleId): bool
    {
        $currentParent = $parentRoleId;

        while ($currentParent) {
            if ($currentParent === $roleId) {
                return true;
            }

            $parent = \Spatie\Permission\Models\Role::find($currentParent);
            if (! $parent || ! $parent->parent_role_id) {
                break;
            }

            $currentParent = $parent->parent_role_id;
        }

        return false;
    }
}
