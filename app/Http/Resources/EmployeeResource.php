<?php

namespace App\Http\Resources;


use App\Enums\Role as EnumRole;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $blockRoles = [
            EnumRole::ADMIN,
            EnumRole::CUSTOMER,
            EnumRole::DELIVERY_BOY,
            EnumRole::WAITER,
            EnumRole::CHEF,
        ];

        // Find the user's specific employee role (e.g. Branch Manager, POS Operator, Staff)
        $employeeRole = $this->roles->first(function ($role) use ($blockRoles) {
            return !in_array($role->id, $blockRoles);
        }) ?? $this->roles->first();

        return [
            "id"           => $this->id,
            "name"         => $this->name,
            "username"     => $this->username,
            "email"        => $this->email,
            "branch_id"    => $this->branch_id,
            "phone"        => $this->phone === null ? '' : $this->phone,
            "status"       => $this->status,
            "role_id"      => optional($employeeRole)->id,
            "role"         => optional($employeeRole)->name,
            "image"        => $this->image,
            "country_code" => $this->country_code,
        ];
    }
}
