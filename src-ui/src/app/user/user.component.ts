import { Component, OnInit } from '@angular/core';
import { UserService } from '../_service/user.service';
import {DataTableModule,SharedModule} from 'primeng/primeng';
import {DialogModule} from 'primeng/primeng';
import {PasswordModule} from 'primeng/primeng';

@Component({
  selector: 'app-user',
  templateUrl: './user.component.html',
  styleUrls: ['./user.component.css']
})

export class UserComponent implements OnInit {

  displayDialog: boolean;
  
      user: any;
      
      selectedUser: any;
      
      newUser: boolean;
  
      users: any[];

  constructor(private userService: UserService) { }

  ngOnInit() {
    this.reload();
 }

 reload()
 {
    this.userService.getList().then(users => this.users = users);
 }

showDialogToAdd() {
    this.newUser = true;
    this.user = {isNew:true};
    this.displayDialog = true;
}

closeDialog() {
  this.displayDialog = false;
}
save() {
    let users = [...this.users];
    if (this.newUser)
        users.push(this.user);
    else
        users[this.findSelectedCarIndex()] = this.user;

    this.userService.save(this.user).then(user => this.reload());

    this.users = users;
    this.user = null;
    this.displayDialog = false;
}

delete() {
   this.userService.delete(this.user).then(user => this.reload());
    let index = this.findSelectedCarIndex();
    this.users = this.users.filter((val, i) => i != index);
    this.user = null;    
    this.displayDialog = false;
}    

onRowSelect(event) {
    this.newUser = false;
    this.user = this.cloneCar(event.data);
    this.displayDialog = true;
}

cloneCar(c: any): any {
    let user = {};
    for (let prop in c) {
        user[prop] = c[prop];
    }
    return user;
}

findSelectedCarIndex(): number {
    return this.users.indexOf(this.selectedUser);
}

}


