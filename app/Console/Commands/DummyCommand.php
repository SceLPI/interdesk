<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\Message;
use App\Models\Observer;
use App\Models\Ticket;
use App\Models\UserTicketAccess;
use App\User;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class DummyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dummy:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    /* @var Generator */
    private $faker;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->faker = Factory::create();

        if ( env('APP_DEBUG') != true && env('APP_ENV') != 'development')
        {
            echo "This command can be used only on develipment enviroment and with debug activated";
            exit;
        }

//        $roles = $this->createRoles(5);
        $departments = $this->createDepartments(10);
        $users = $this->createUsers(30, $roles, $departments);
        $tickets = $this->createTickets(1000, $users, $departments);
        $this->createMessages(5, $tickets);
        $this->createAccess($tickets);
        $this->createObservers($tickets, $users);
    }

    private function createRoles($ammount)
    {
        $roles = [];
        foreach( range(0,$ammount) as $i )
        {
            $role = new Role;
            $role->display_name = $this->faker->jobTitle;
            $role->name = preg_replace("/\W/", "", $role->display_name);
            $role->save();
            $roles[] = $role;
        }
        return $roles;
    }

    private function createDepartments($ammount)
    {
        $departments = [];
        foreach( range(0,$ammount) as $i )
        {
            $department = new Department;
            $department->name = $this->faker->company;
            $department->save();
            $departments[] = $department;
        }

        return $departments;
    }

    private function createUsers($ammount, &$roles, &$departments)
    {
        $users = [];
        foreach( range(0,$ammount) as $i )
        {
            $user = new User;
//            $user->role_id = $this->faker->randomElement($roles)->id;
            $user->name = $this->faker->name();
            $user->email = $this->faker->safeEmail;
//            $user->avatar = "";
            $user->is_admin = $this->faker->randomElement([true, false, false, false, false, false, false, false, false, false, false, false, false, false,]);
            $user->department_id = $this->faker->randomElement($departments)->id;
            $user->password = Hash::make(123456);
            $user->save();
            $users[] = $user;
        }

        return $users;
    }

    private function createTickets($ammount, &$users, &$departments)
    {
        $tickets = [];
        foreach( range(0,$ammount) as $i )
        {
            $ticket = new Ticket;
            $ticket->user_id = $this->faker->randomElement($users)->id;
            $ticket->small_title = $this->faker->text(50);
            $ticket->title = $this->faker->realText(120);
            $ticket->prior_id = $this->faker->numberBetween(1,3);
            $ticket->department_id = $this->faker->randomElement(
                array_merge(
                    $departments, [new Department,new Department,new Department,new Department,])
            )->id;
            $ticket->limit_date = $this->faker->dateTimeThisMonth;
            $ticket->content = $this->faker->text(900);
            $ticket->status_id = $this->faker->numberBetween(1,4);
            $ticket->agent_user_id = $ticket->department_id !== null ?
                $this->faker->randomElement(array_merge($users, [new User,new User,new User,new User,new User,new User,new User,new User,new User,new User,new User,new User,new User,new User,]))->id :
                $this->faker->randomElement($users)->id;
            $ticket->rating = $ticket->status_id == 3 ? $this->faker->numberBetween(2,5) : null;
            $ticket->save();
            $tickets[] = $ticket;
        }

        return $tickets;
    }

    private function createMessages($ammount, &$tickets)
    {
        $messages = [];
        foreach($tickets as $ticket) {
            foreach( range(1, $this->faker->numberBetween(1,$ammount)) as $i) {
                $message = new Message;
                $message->user_id = $this->faker->randomElement([ $ticket->user_id, $ticket->agent_id ]) ?: $ticket->user_id;
                $message->ticket_id = $ticket->id;
                $message->message = $this->faker->text(50);
                $message->created_at = $this->faker->dateTimeThisMonth();
                $message->save();
                $messages[] = $message;
            }
        }
        return $messages;
    }

    private function createAccess(&$tickets) {
//        $access = [];
//        foreach($tickets as $ticket) {
//            $acces = new UserTicketAccess;
//            $acces->user_id =
//        }
//        return $access;
    }

    private function createObservers(&$tickets, &$users)
    {
        $observers = [];

        foreach($tickets as $ticket) {
            $observersCollects = $this->faker->randomElements($users, $this->faker->numberBetween(0,3), false);
            foreach ($observersCollects as $observerCollect ) {
                $observer = new Observer;
                $observer->ticket_id = $ticket->id;
                $observer->user_id = $observerCollect->id;
                $observer->save();
                $observers[] = $observer;
            }
        }
        return $observers;
    }
}
