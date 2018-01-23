import { Injectable } from '@angular/core';
import { Http, Headers, Response } from '@angular/http';
import { Observable } from 'rxjs/Observable';
import 'rxjs/add/operator/map';
import 'rxjs/add/operator/catch';
import { MessageService } from 'primeng/components/common/messageservice';



import { ConfigurationService } from './configuration.service';

@Injectable()
export class AuthenticationService 
{
   
    constructor(private http: Http, private messageService: MessageService, private config:ConfigurationService ) { 
        
    }
     user:any= {};
    currentUser() {
    
      this.user = JSON.parse( localStorage.getItem('currentUser'));
      return this.user;
    }
    login(username: string, password: string) {
        
         let inputuser = {'username' : username, 'password' : password};
        return this.http.post(this.config.apiHost +'auth/Authorize', inputuser)
            .map((response: Response) => {
                // login successful if there's a jwt token in the response
                console.log(response);
                if (response) {
                    // store user details and jwt token in local storage to keep user logged in between page refreshes
                    localStorage.setItem('currentUser', JSON.stringify(response.json()));
                    console.log( JSON.stringify(response.json()));
                }
            })
            .catch((err:Response) => {
                let details = err.json();
                this.messageService.add({severity: 'error', 
                summary: 'Login Error', 
                detail: 'Error during login. Check password and username'  + JSON.stringify(err.json().error) });
                return Observable.throw(new Error(details));
             });
                
    }

    logout() {
        // remove user from local storage to log user out
        localStorage.removeItem('currentUser');
        this.user={};
    }
}