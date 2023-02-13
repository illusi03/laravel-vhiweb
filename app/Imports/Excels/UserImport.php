<?php

namespace App\Imports\Excels;

use App\Models\User;
use DB;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImport implements ToCollection, WithHeadingRow, SkipsOnError, WithValidation
{
    use Importable, SkipsErrors;
    
    public function collection(Collection $rows)
    {
        // NOTE:
        // WithBatchInsert TIDAK AKAN WORK apabila kita memakai Collection.
        // Jika memakai Validator
        // Validator::make($rows->toArray(), [
        //   '*.email' => 'required|unique:users',
        // ])->validate();
        try {
            DB::beginTransaction();
            foreach ($rows as $row) {
                $user = User::create([
                    'email' => $row['email'],
                    'name' => $row['name'],
                    'password' => Hash::make($row['password'])
                    // 'password' => Hash::make('password'),
                ]);
                // Jika memakai Relasi, bisa langsung add aja.
                // $user->todos()->create([$row['todos']])
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }

    public function rules(): array
    {
        return [
            // * = ALL ROWS
            //. columns = col name
            '*.name' => 'required|string',
            '*.email' => 'required|string|unique:users,email,NULL,id,deleted_at,NULL',
            '*.password' => 'required|string',
            '*.gender' => 'in:male,female',
            '*.telp' => 'string'
            // 'photo' => 'file|image|max:10240',
        ];
    }
}
