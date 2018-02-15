import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthenticationService } from '../_service/authentication.services';
import { NgModule }      from '@angular/core';
import { ConfigurationService } from '../_service/configuration.service';
import { MessageService } from 'primeng/components/common/messageservice';


@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  moduleId: module.id,
})
export class LoginComponent implements OnInit {

  user: any = {};
  loading = false;
  returnUrl: string;
  env:any;
 
  constructor(
      private route: ActivatedRoute,
      private router: Router,
      private authenticationService: AuthenticationService,
  
      private config: ConfigurationService,
      private messageService: MessageService) { }

      cliks: number= 0;

  ngOnInit() {
    
      // reset login status
      this.authenticationService.logout();

    this.user={};

      // get return url from route parameters or default to '/'
      this.returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/admin/home';
  }



  login() {

      this.loading = true;
      console.log('logging in..' );
      this.authenticationService.login(this.user.username, this.user.password)
          .subscribe(
              data => {
                  this.router.navigate([this.returnUrl]);
              },
              error => {
                  this.messageService.add({severity: 'error',
                  summary: 'Login Error',
                  detail: 'Error during login. Check password and username.'  });
                  this.loading = false;
              });
  }

  count() {
    console.log("CLICK");
    this.cliks++;
  }

}
