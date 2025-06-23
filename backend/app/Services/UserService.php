<?php

namespace App\Services;
use App\Models\User;
use App\Repositories\Interfaces\UserRepository;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    private $userRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        return $this->userRepository->all()->makeVisible('id');
    }

    public function store($data)
    {
        $user = $this->userRepository->create($data);

        return $user;
    }

    public function show($id)
    {
        return $this->userRepository->find($id);
    }

    public function showWithId($id)
    {
        return $this->userRepository->find($id)->makeVisible('id');
    }

    public function update($data, $id)
    {
        $user = $this->userRepository->update($data, $id);
        return $user;
    }

    public function destroy($id)
    {
        $this->userRepository->delete($id);
        return;
    }

    public function getRentals(User $user, $page, $limit, $status) {
        $query = $user->rentals()->with('rentalDetails.book');
        if (!empty($status)) {
            $query = $query->where('status', $status);
        }

        $rentals = $query->orderBy('created_at', 'desc')
                        ->paginate($limit);

        return $rentals;
    }
}
