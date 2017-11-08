import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { AuthenticationService } from '../_service/authentication.services';
import { AlertService } from '../_service/alert.service';
import { NgModule }      from '@angular/core';

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

  constructor(
      private route: ActivatedRoute,
      private router: Router,
      private authenticationService: AuthenticationService,
      private alertService: AlertService) { }

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
                  this.alertService.error(error);
                  console.log("error");
                  this.loading = false;
              });
  }

  count() {
    console.log("CLICK");
    this.cliks++;
  }

}
